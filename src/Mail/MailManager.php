<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Mail;

use Rovota\Core\Auth\Interfaces\Identity;
use Rovota\Core\Auth\User;
use Rovota\Core\Facades\Registry;
use Rovota\Core\Http\RequestManager;
use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Localization\LocalizationManager;
use Rovota\Core\Mail\Interfaces\Mailable;
use Throwable;

/**
 * @internal
 */
final class MailManager
{

	protected static array $config = [];

	protected static array $defaults = [];

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	public static function initialize(): void
	{
		self::$defaults = [
			'from' => [
				'name' => Registry::string('mail_from_name', Registry::string('site_name')),
				'address' => Registry::string('mail_from_address', 'no-reply@'.RequestManager::getRequest()->targetHost()),
			],
			'reply_to' => [
				'name' => Registry::string('mail_reply_to_name', Registry::string('site_name')),
				'address' => Registry::string('mail_reply_to_address', 'info@'.RequestManager::getRequest()->targetHost()),
			],
		];
	}

	// -----------------

	public static function make(string|null $class = null): Mailable
	{
		$class = $class === null ? Mail::class : $class;
		return new $class();
	}

	// -----------------

	public static function getDefaults(): array
	{
		return self::$defaults;
	}

	public static function setFrom(string $name, string $address): void
	{
		self::$defaults['from'] = ['name' => trim($name), 'address' => trim($address)];
	}

	public static function setReplyTo(string $name, string $address): void
	{
		self::$defaults['reply_to'] = ['name' => trim($name), 'address' => trim($address)];
	}

	// -----------------

	public static function getConfig(): array
	{
		return self::$config;
	}

	public static function setAlwaysTo(string $name, string $address): void
	{
		self::$config['always_to'] = ['name' => trim($name), 'address' => trim($address)];
	}

	// -----------------

	public static function getIdentityData(Identity|string|int $name, string|null $address): array|null
	{
		if ($address !== null) {
			return [
				'name' => trim($name),
				'address' => trim($address),
				'language' => LocalizationManager::getActiveLanguage(),
			];
		}

		if ($name instanceof Identity) {
			return [
				'name' => $name->getName(),
				'address' => $name->getEmail(),
				'language' => $name->getLanguage(),
			];
		}

		try {
			$user = User::find($name);
			if ($user instanceof User) {
				return [
					'name' => $user->nickname,
					'address' => $user->email,
					'language' => $user->language,
				];
			}
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable);
			return null;
		}

		return null;
	}

}