<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Facades;

use Rovota\Core\Mail\Interfaces\Mailable;
use Rovota\Core\Mail\MailManager;
use Rovota\Core\Views\View;

final class Mail
{

	protected function __construct()
	{
	}

	// -----------------

	public static function make(string|null $class = null): Mailable
	{
		return MailManager::make($class);
	}

	// -----------------

	public static function alwaysFrom(string $name, string $address): void
	{
		MailManager::setFrom($name, $address);
	}

	public static function alwaysReplyTo(string $name, string $address): void
	{
		MailManager::setReplyTo($name, $address);
	}

	public static function alwaysTo(string $name, string $address): void
	{
		MailManager::setAlwaysTo($name, $address);
	}

	// -----------------

	public static function plain(string $content): Mailable
	{
		return MailManager::make()->plain($content);
	}

	public static function view(View|string $name, string|null $source = null): Mailable
	{
		return MailManager::make()->view($name, $source);
	}

}