<?php
/** @noinspection SpellCheckingInspection */

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Kernel;

use Envms\FluentPDO\Exception;
use Rovota\Core\Access\Features\FeatureManager;
use Rovota\Core\Auth\AccessManager;
use Rovota\Core\Auth\AuthManager;
use Rovota\Core\Http\Throttling\LimitManager;
use Rovota\Core\Mail\MailManager;
use Rovota\Core\Routing\RouteManager;
use Rovota\Core\Storage\StorageManager;
use Rovota\Core\Validation\ValidationManager;
use Rovota\Core\Views\ViewManager;

final class Application
{

	/**
	 * @throws Exception
	 */
	public static function start(): void
	{

//		LoggingManager::initialize();
//		CastManager::initialize();
//		CacheManager::initialize();
//		ConnectionManager::initialize();
		StorageManager::initialize();

//		self::$registry = new Registry();

		ValidationManager::initialize();
//		CookieManager::initialize();
//		SessionManager::initialize();
//		RequestManager::initialize();

		LimitManager::initialize();
//		LocalizationManager::initialize();
		ViewManager::initialize();
		MailManager::initialize();
//		ResponseManager::initialize();
		AuthManager::initialize();
		AccessManager::initialize();
//		MiddlewareManager::initialize();
		RouteManager::initialize();
		FeatureManager::initialize();

		RouteManager::run();
	}

}