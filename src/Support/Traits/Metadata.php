<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Support\Traits;

use Rovota\Core\Support\Collection;
use Rovota\Core\Support\Meta;

trait Metadata
{
	/**
	 * @var Collection<string, Meta>
	 */
	public Collection $meta;

	// -----------------

	public function meta(string $name, mixed $default = null): mixed
	{
		return $this->meta->get($name)?->value ?? $default;
	}

	// -----------------

	public function setMeta(string $name, mixed $value, bool $delete_if_null = true): bool
	{
		if ($this->meta->has($name)) {
			$model = $this->meta->get($name);
			if ($delete_if_null && $value === null) {
				return $model->forceDelete();
			}
			$model->value = $value;
			return $model->save();
		}

		if ($delete_if_null && $value === null) {
			return true;
		}

		$model = new $this->meta_model();
		$model->{$this->meta_foreign_key} = $this->getId();
		$model->name = $name;
		$model->value = $value;

		if ($model->save()) {
			$this->meta->put($name, $model);
			return true;
		}

		return false;
	}

	public function getMeta(string $name): Meta|null
	{
		return $this->meta->get($name);
	}

	public function deleteMeta(string $name, bool $permanent = false): bool
	{
		$model = $this->meta->pull($name);
		return $model === null || $model->delete($permanent);
	}

	// -----------------

	protected function loadMeta(): void
	{
		$this->meta = $this->{'meta_model'}::where([$this->meta_foreign_key => $this->getId()])->getBy('name');
	}

	// -----------------

	protected function prepareMeta(): void
	{
		$this->meta = new Collection();
	}

}