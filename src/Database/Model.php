<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database;

use BackedEnum;
use JsonSerializable;
use Rovota\Core\Cache\CacheManager;
use Rovota\Core\Database\Traits\QueryFunctions;
use Rovota\Core\Facades\DB;
use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Support\Helpers\Arr;
use Rovota\Core\Support\Moment;
use Rovota\Core\Support\Text;
use Rovota\Core\Support\Traits\Conditionable;
use Rovota\Core\Support\Traits\Errors;
use Rovota\Core\Support\Traits\Macroable;
use Rovota\Core\Validation\Traits\ModelValidation;
use stdClass;
use Throwable;
use TypeError;

/**
 * @property-read Moment|null $last_changed Only available when composite attributes are enabled.
 */
abstract class Model implements JsonSerializable
{
	use ModelValidation, Errors, Macroable, QueryFunctions, Conditionable;

	protected string|null $table = null;
	protected string|null $connection = null;
	protected string $primary_key = 'id';
	protected string|null $cache_key = null;

	protected array $attributes = [];
	protected array $attributes_modified = [];

	protected array $restricted = [];
	protected array $fillable = [];
	protected array $guarded = [];
	protected array $hidden = [];
	protected array $casts = [];

	protected bool $auto_increment = true;
	protected bool $manage_timestamps = true;
	protected bool $apply_default_casts = true;

	protected bool $enable_composites = true;

	public bool $is_stored = false;

	// -----------------

	public function __construct(array $attributes = [])
	{
		if ($this->apply_default_casts || $this->manage_timestamps) {
			$this->setOption('casts', [
				'created' => 'moment',
				'edited' => 'moment',
				'deleted' => 'moment',
			]);
			$this->hide(['created', 'edited', 'deleted']);
		}

		if ($this->table === null) {
			$this->table = $this->guessTableName();
		}
		if ($this->connection === null) {
			$this->connection = DatabaseManager::getDefault();
		}

		$this->setAttributes($attributes);

		$this->attemptConfigurations();
	}

	public function __toString(): string
	{
		return $this->toJson();
	}

	// -----------------
	// Magic Methods

	public function __set(string $name, mixed $value): void
	{
		$this->setAttribute($name, $value);
	}

	public function __get(string $name): mixed
	{
		return $this->getAttribute($name);
	}

	public function __isset(string $name): bool
	{
		return $this->hasAttribute($name);
	}

	public function __unset(string $name): void
	{
		$this->revert($name);
	}

	// -----------------
	// Static Helpers

	/**
	 * @internal
	 */
	protected static function newInstance(array $attributes = [], bool $stored = false): static
	{
		$instance = new static($attributes);
		$instance->is_stored = $stored;

		return $instance;
	}

	/**
	 * @internal
	 */
	public static function newFromBuilder(stdClass $class): static
	{
		$instance = self::newInstance([], true);

		foreach ($class as $name => $value) {
			$instance->setRawAttribute($name, $value);
		}

		$instance->eventModelLoaded();
		// $key = CacheManager::get()->lastModifiedKey();
		// $instance->saveCacheKey(CacheManager::get()->lastModifiedKey());

		return $instance;
	}

	// -----------------

	public function original(string|null $attribute = null): mixed
	{
		return $attribute === null ? $this->attributes : $this->attributes[$attribute] ?? null;
	}

	public function attribute(string $name, mixed $default = null): mixed
	{
		return $this->getAttribute($name) ?? $default;
	}

	// -----------------

	public function toArray(): array
	{
		return as_bucket(array_merge($this->attributes, $this->attributes_modified))->except($this->hidden)->toArray();
	}

	public function toJson(): string
	{
		return json_encode_clean($this->jsonSerialize());
	}

	// -----------------

	public function fresh(): static|null
	{
		try {
			return static::find($this->attributes[$this->primary_key], $this->primary_key);
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable);
			return null;
		}
	}

	public function reload(): void
	{
		$new = $this->fresh();
		$this->attributes = [];
		$this->attributes_modified = [];
		foreach ($new->original() as $name => $value) {
			$this->attributes[$name] = $value;
		}
		$this->eventModelReloaded();
	}

	public function revert(string|array|null $attribute = null): void
	{
		if (is_array($attribute)) {
			foreach ($attribute as $item) {
				$this->revert($item);
			}
		} else {
			if ($attribute !== null) {
				unset($this->attributes_modified[$attribute]);
				$this->eventModelRevertedAttribute($attribute);
			} else {
				$this->attributes_modified = [];
				$this->eventModelReverted();
			}
		}
	}

	// -----------------

	public function getLastChangedAttribute(): Moment|null
	{
		return $this->getAttribute('edited') ?? $this->getAttribute('created') ?? null;
	}

	// -----------------

	public function isChanged(array|string|null $attributes = null): bool
	{
		if ($attributes === null) {
			return empty($this->attributes_modified) === false;
		}

		$attributes = is_array($attributes) ? $attributes : [$attributes];
		foreach ($attributes as $attribute) {
			if (isset($this->attributes_modified[$attribute])) {
				return true;
			}
		}

		return false;
	}

	public function isOriginal(array|string|null $attributes = null): bool
	{
		return $this->isChanged($attributes) === false;
	}

	public function isAllowedValue(string $attribute, mixed $value): bool
	{
		if ($value === null) {
			return true;
		}

		if (isset($this->casts[$attribute])) {
			if (is_string($this->casts[$attribute])) {
				$this->casts[$attribute] = [$this->casts[$attribute]];
			}
			if (CastManager::isAllowedValueForCast($this->casts[$attribute], $value) === false) {
				throw new TypeError(
					sprintf("Value must be supported by the '%s' cast, %s given", $this->casts[$attribute][0], is_object($value) ? $value::class : gettype($value))
				);
			}
		}

		if (isset($this->restricted[$attribute])) {
			$allowed = $this->restricted[$attribute];

			if (is_string($allowed) || $allowed instanceof BackedEnum) {
				if (Arr::contains($allowed::cases(), $value)) {
					throw new TypeError(
						sprintf('Value must be of type %s, %s given.', $allowed, is_object($value) ? $value::class : gettype($value))
					);
				}
			} else {
				if (Arr::contains($allowed, $value) === false) {
					return false;
				}
			}
		}

		return true;
	}

	public function isHidden(string $attribute): bool
	{
		return isset($this->hidden[$attribute]);
	}

	public function isGuarded(string $attribute): bool
	{
		return $this->isAttributeFillable($attribute) === false;
	}

	public function isFillable(string $attribute): bool
	{
		return $this->isAttributeFillable($attribute) === true;
	}

	public function isRestricted(string $attribute): bool
	{
		return isset($this->restricted[$attribute]);
	}

	public function isDeleted(): bool
	{
		return $this->getAttribute('deleted') !== null;
	}

	public function isNotDeleted(): bool
	{
		return $this->getAttribute('deleted') === null;
	}

	public function isStored(): bool
	{
		return $this->is_stored;
	}

	// -----------------

	public function fill(array $attributes): static
	{
		$this->setAttributes($attributes);
		return $this;
	}

	// -----------------

	public function save(): bool
	{
		try {
			if ($this->is_stored) {
				if (empty($this->attributes_modified) === false) {
					if (!isset($this->attributes_modified['edited']) && $this->manage_timestamps) {
						$this->attributes_modified['edited'] = now();
					}
					static::where($this->primary_key, $this->attributes[$this->primary_key])->update($this->attributes_modified);
					$this->attributes = array_merge($this->attributes, $this->attributes_modified);
				}
			} else {
				$this->attributes = array_merge($this->attributes, $this->attributes_modified);
				if (empty($this->attributes) === false) {
					if (!isset($this->attributes['created']) && $this->manage_timestamps) {
						$this->attributes['created'] = now();
					}
					static::insert($this->attributes);

					if ($this->auto_increment) {
						$this->attributes[$this->primary_key] = DB::connection($this->connection)->lastId();
					}

					$this->reload();
				}
			}

			// TODO: Call save() on relationships

			$this->cleanAfterSave();

		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable);
			return false;
		}

		return true;
	}

	public function delete(bool $permanent = false): bool
	{
		return $permanent ? $this->forceDelete() : $this->softDelete();
	}

	public function forceDelete(): bool
	{
		try {
			if ($this->is_stored) {
				$affected = static::where($this->primary_key, $this->attributes[$this->primary_key])->forceDelete();
				$this->cleanAfterForceDelete();
			}
			return ($affected ?? 0) === 1;
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable);
			return false;
		}
	}

	public function softDelete(): bool
	{
		try {
			if ($this->is_stored) {
				$affected = static::where($this->primary_key, $this->attributes[$this->primary_key])->softDelete();
				$this->cleanAfterSoftDelete();
			}
			return ($affected ?? 0) === 1;
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable);
			return false;
		}
	}

	public function recover(): bool
	{
		try {
			if ($this->is_stored) {
				$affected = static::where($this->primary_key, $this->attributes[$this->primary_key])->recover();
				$this->cleanAfterRecover();
			}
			return ($affected ?? 0) === 1;
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable);
			return false;
		}
	}

	// -----------------

	public function clearFromCache(): void
	{
		if ($this->cache_key !== null) {
			CacheManager::get()->forget($this->cache_key);
		}
	}

	public function saveCacheKey(string $key): static
	{
		$this->cache_key = $key;
		return $this;
	}

	public function getCacheKey(): string|null
	{
		return $this->cache_key;
	}

	// -----------------

	protected function hasAttribute(string $name): bool
	{
		return isset($this->attributes[$name]) || isset($this->attributes_modified[$name]);
	}

	protected function setAttribute(string $name, mixed $value): void
	{
		if ($this->validateAssignment($name, $value) && $this->isAllowedValue($name, $value)) {
			if ($this->enable_composites) {
				$accessor = sprintf('set%sAttribute', Text::pascal($name));
				if (method_exists($this, $accessor)) {
					$this->{$accessor}($value);
					return;
				}
			}
			if (isset($this->attributes[$name]) === false || $this->attributes[$name] !== $value) {
				$this->attributes_modified[$name] = $value;
			}
		}
	}

	protected function setAttributes(array $attributes): void
	{
		foreach ($attributes as $name => $value) {
			if ($this->isAttributeFillable($name)) {
				$this->setAttribute($name, $value);
			}
		}
	}

	protected function getAttribute(string $name): mixed
	{
		$value = $this->attributes_modified[$name] ?? $this->attributes[$name] ?? null;

		if ($this->enable_composites) {
			$accessor = sprintf('get%sAttribute', Text::pascal($name));
			if (method_exists($this, $accessor)) {
				return $this->{$accessor}();
			}
		}

		return $value;
	}

	// -----------------

	protected function isAttributeFillable(string $name): bool
	{
		if (empty($this->fillable) === false) {
			return in_array($name, $this->fillable);
		}

		if (empty($this->guarded) === false) {
			return in_array($name, $this->guarded) === false;
		}

		return false;
	}

	// -----------------
	// Casting

	public function hasCast(string $attribute): bool
	{
		return isset($this->casts[$attribute]);
	}

	public function getCast(string $attribute): mixed
	{
		return $this->casts[$attribute] ?? null;
	}

	protected function castFromRaw(string $attribute, mixed $value): mixed
	{
		if (!isset($this->casts[$attribute]) || $value === null) {
			return $value;
		}
		return CastManager::castFromRaw($value, $this->casts[$attribute]);
	}

	protected function castToRaw(string $attribute, mixed $value): mixed
	{
		if (!isset($this->casts[$attribute]) || $value === null) {
			return $value;
		}
		return CastManager::castToRaw($value, $this->casts[$attribute]);
	}

	// -----------------
	// Configuration

	public function setValidateOnAssignment(bool $state): void
	{
		$this->validate_on_assignment = $state;
	}

	public function setRejectInvalidAssignments(bool $state): void
	{
		$this->reject_invalid_assignments = $state;
	}

	public function getId(): string
	{
		return $this->attribute($this->primary_key);
	}

	public function getTable(): string|int
	{
		return $this->table;
	}

	public function getConnection(): string|int
	{
		return $this->connection;
	}

	public function getPrimaryKey(): string
	{
		return $this->primary_key;
	}

	// -----------------

	public function hide(array|string $attributes): static
	{
		if (is_array($attributes)) {
			foreach ($attributes as $attribute) {
				$this->hide($attribute);
			}
		} else {
			$this->hidden[] = $attributes;
		}
		return $this;
	}

	public function hideWhen(array|string $attributes, callable $callback): static
	{
		if ($callback($this)) {
			$this->hide($attributes);
		}
		return $this;
	}

	public function show(array|string $attributes): static
	{
		if (is_array($attributes)) {
			foreach ($attributes as $attribute) {
				$this->show($attribute);
			}
		} else {
			if (($key = array_search($attributes, $this->hidden)) !== false) {
				unset($this->hidden[$key]);
			}
		}
		return $this;
	}

	public function showWhen(array|string $attributes, callable $callback): static
	{
		if ($callback($this)) {
			$this->show($attributes);
		}
		return $this;
	}

	public function guard(array|string $attributes): static
	{
		if (is_array($attributes)) {
			foreach ($attributes as $attribute) {
				$this->guard($attribute);
			}
		} else {			
			if (empty($this->fillable) === false) {
				if (($key = array_search($attributes, $this->fillable)) !== false) {
					unset($this->fillable[$key]);
				}
			} else {
				if (in_array($attributes, $this->guarded) === false) {
					$this->guarded[] = $attributes;
				}
			}
		}
		return $this;
	}

	public function fillable(array|string $attributes): static
	{
		if (is_array($attributes)) {
			foreach ($attributes as $attribute) {
				$this->fillable($attribute);
			}
		} else {
			if (empty($this->guarded) === false) {
				if (($key = array_search($attributes, $this->guarded)) !== false) {
					unset($this->guarded[$key]);
				}
			} else {
				if (in_array($attributes, $this->fillable) === false) {
					$this->fillable[] = $attributes;
				}
			}
		}
		return $this;
	}

	protected function setOption(string $name, mixed $value): void
	{
		if (is_array($value)) {
			foreach ($value as $item => $data) {
				$this->{$name}[$item] = $data;
			}
		} else {
			$this->{$name} = $value;
		}
	}

	// -----------------
	// Internal Helpers

	/**
	 * @internal
	 */
	public function jsonSerialize(): array
	{
		return $this->toArray();
	}

	/**
	 * @internal
	 */
	public function setRawAttribute(string $name, mixed $value): void
	{
		$this->attributes[$name] = $this->castFromRaw($name, $value);
	}

	protected function guessTableName(): string
	{
		$name = Text::snake(Text::afterLast(static::class, '\\'));
		$last_word = Text::afterLast($name, '_');
		return str_replace($last_word, Text::plural($last_word), $name);
	}

	// -----------------
	// Cleanup

	protected function cleanAfterSave(): void
	{
		$this->is_stored = true;
		$this->attributes_modified = [];
		$this->eventModelSaved();
		$this->clearFromCache();
	}

	protected function cleanAfterForceDelete(): void
	{
		$this->is_stored = false;
		$this->eventModelForceDeleted();
		$this->clearFromCache();
	}

	protected function cleanAfterSoftDelete(): void
	{
		$this->attributes['deleted'] = now();
		$this->eventModelSoftDeleted();
		$this->clearFromCache();
	}

	protected function cleanAfterRecover(): void
	{
		$this->attributes['deleted'] = null;
		$this->eventModelRecovered();
		$this->clearFromCache();
	}

	// -----------------
	// Events

	public function eventModelLoaded(): void
	{
	}

	public function eventModelCreated(): void
	{
	}

	public function eventModelSaved(): void
	{
	}

	public function eventModelForceDeleted(): void
	{
	}

	public function eventModelSoftDeleted(): void
	{
	}

	protected function eventModelRecovered(): void
	{
	}

	protected function eventModelReloaded(): void
	{
	}

	protected function eventModelReverted(): void
	{
	}

	protected function eventModelRevertedAttribute(string $attribute): void
	{
	}

	// -----------------

	private function attemptConfigurations(): void
	{
		if (method_exists($this, 'prepareMeta')) {
			$this->prepareMeta();
		}

		if (method_exists($this, 'preparePermissions')) {
			$this->preparePermissions();
		}
	}

}