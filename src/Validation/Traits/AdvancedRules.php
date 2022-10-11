<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Validation\Traits;

use Rovota\Core\Facades\DB;
use Rovota\Core\Http\Client\HibpClient;
use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Validation\Enums\FilterAction;
use Rovota\Core\Validation\ValidationManager;
use Throwable;

trait AdvancedRules
{

	protected function ruleRegex(string $field, string $data, string $pattern): bool
	{
		if (!preg_match($pattern, $data)) {
			$this->addError($field, 'regex');
			return false;
		}
		return true;
	}

	protected function ruleNotRegex(string $field, string $data, string $pattern): bool
	{
		if (preg_match($pattern, $data)) {
			$this->addError($field, 'not_regex');
			return false;
		}
		return true;
	}

	protected function ruleUnique(string $field, mixed $data, array|string $options): bool
	{
		if (!is_string($data) && !is_int($data)) {
			$data = (string)$data;
		}

		$occurrences = $this->getOccurrences($this->processDatabaseOptions($field, $options), $data);
		if ($occurrences > 0) {
			$this->addError($field, 'unique', [$data, $occurrences]);
			return false;
		}

		return true;
	}

	protected function ruleExists(string $field, mixed $data, array|string $options): bool
	{
		if (!is_string($data) && !is_int($data)) {
			$data = (string)$data;
		}

		$occurrences = $this->getOccurrences($this->processDatabaseOptions($field, $options), $data);
		if ($occurrences === 0) {
			$this->addError($field, 'exists', [$data]);
			return false;
		}

		return true;
	}

	protected function ruleFilter(string $field, mixed $data, string|array $filters): bool
	{
		if (!is_string($data)) {
			return true;
		}

		$filters = is_string($filters) ? [$filters] : $filters;
		foreach ($filters as $filter_name) {
			if (ValidationManager::hasFilter($filter_name)) {
				$filter = ValidationManager::getFilter($filter_name);

				if ($filter->action === FilterAction::Block && string($data)->lower()->containsAny($filter->values)) {
					$this->addError($field, 'filter');
					return false;
				}

				if ($filter->action === FilterAction::Allow && string($data)->lower()->containsNone($filter->values)) {
					$this->addError($field, 'filter');
					return false;
				}
			}
		}

		return true;
	}

	protected function ruleHash(string $field, mixed $data, array $reference): bool
	{
		if ($data !== hash($reference[0], $reference[1])) {
			$this->addError($field, 'hash');
			return false;
		}
		return true;
	}

	protected function ruleEmail(string $field, mixed $data): bool
	{
		if (filter_var($data, FILTER_VALIDATE_EMAIL, FILTER_NULL_ON_FAILURE) === null) {
			$this->addError($field, 'email');
			return false;
		}
		return true;
	}

	protected function ruleDifferent(string $field, mixed $data, string $target): bool
	{
		if ($data === $this->data->get($target)) {
			$this->addError($field, 'different');
			return false;
		}
		return true;
	}

	protected function ruleRequiredIfEnabled(string $field, mixed $data, string $target): bool
	{
		if ($this->data->bool($target) && $data === null) {
			$this->addError($field, 'required');
			return false;
		}
		return true;
	}

	protected function ruleRequiredIfDisabled(string $field, mixed $data, string $target): bool
	{
		if ($this->data->bool($target) === false && $data === null) {
			$this->addError($field, 'required');
			return false;
		}
		return true;
	}

	protected function ruleEqual(string $field, mixed $data, string $target): bool
	{
		if ($data !== $this->data->get($target)) {
			$this->addError($field, 'equal');
			return false;
		}
		return true;
	}

	protected function ruleHibp(string $field, mixed $data, int|null $threshold): bool
	{
		if (!is_string($data)) {
			return true;
		}
		$hibp = new HibpClient();
		$matches = $hibp->countPasswordMatches(sha1($data));
		if ($matches > $threshold ?? 0) {
			$this->addError($field, 'hibp', [$matches, $threshold ?? 0]);
			return false;
		}
		return true;
	}

	// -----------------

	protected function getOccurrences(array $config, mixed $data): int
	{
		try {
			if ($config['model'] === null) {
				$occurrences = DB::connection($config['connection'])->table($config['table'])->where($config['column'], $data)->count();
			} else {
				$occurrences = $config['model']::where($config['column'], $data)->count();
			}
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable);
			return 0;
		}

		return $occurrences ?? 0;
	}

}