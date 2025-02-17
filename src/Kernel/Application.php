<?php
/** @noinspection SpellCheckingInspection */

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Kernel;

use Rovota\Core\Access\Features\FeatureManager;
use Rovota\Core\Auth\AccessManager;
use Rovota\Core\Auth\AuthManager;
use Rovota\Core\Http\Throttling\LimitManager;
use Rovota\Core\Validation\ValidationManager;

final class Application
{

	public static function start(): void
	{

//		LoggingManager::initialize();
//		CastManager::initialize();
//		CacheManager::initialize();
//		ConnectionManager::initialize();
//		StorageManager::initialize();

//		self::$registry = new Registry();

		ValidationManager::initialize();
//		CookieManager::initialize();
//		SessionManager::initialize();
//		RequestManager::initialize();

		LimitManager::initialize();
//		LocalizationManager::initialize();
//		ViewManager::initialize();
//		MailManager::initialize();
//		ResponseManager::initialize();
		AuthManager::initialize();
		AccessManager::initialize();
//		MiddlewareManager::initialize();
//		RouteManager::initialize();
		FeatureManager::initialize();

//		RouteManager::run();
	}

}