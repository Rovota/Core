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
use Rovota\Core\Addon\AddonManager;
use Rovota\Core\Auth\AccessManager;
use Rovota\Core\Auth\AuthManager;
use Rovota\Core\Cache\CacheManager;
use Rovota\Core\Cookie\CookieManager;
use Rovota\Core\Database\CastManager;
use Rovota\Core\Database\ConnectionManager;
use Rovota\Core\Database\Exceptions\MissingDatabaseConfigException;
use Rovota\Core\Http\RequestManager;
use Rovota\Core\Http\ResponseManager;
use Rovota\Core\Http\Throttling\LimitManager;
use Rovota\Core\Mail\MailManager;
use Rovota\Core\Routing\RouteManager;
use Rovota\Core\Session\Exceptions\MissingSessionConfigException;
use Rovota\Core\Session\Exceptions\UnsupportedDriverException;
use Rovota\Core\Session\SessionManager;
use Rovota\Core\Storage\StorageManager;
use Rovota\Core\Validation\ValidationManager;
use Rovota\Core\Views\ViewManager;

final class Application
{

	// -----------------

	public static Registry $registry;

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * @throws UnsupportedDriverException
	 * @throws MissingDatabaseConfigException
	 * @throws MissingSessionConfigException
	 * @throws Exception
	 */
	public static function start(): void
	{

//		LoggingManager::initialize();
		CastManager::initialize();
//		CacheManager::initialize();
		ConnectionManager::initialize();
		StorageManager::initialize();

//		self::$registry = new Registry();

		ValidationManager::initialize();
		CookieManager::initialize();
		SessionManager::initialize();
//		RequestManager::initialize();

		if (RequestManager::getRequest()->ipAllowed() === false) {
			http_response_code(403); exit;
		}

		LimitManager::initialize();
//		LocalizationManager::initialize();
		ViewManager::initialize();
		MailManager::initialize();
//		ResponseManager::initialize();
		AuthManager::initialize();
		AccessManager::initialize();
		MiddlewareManager::initialize();
		RouteManager::initialize();
		FeatureManager::initialize();
		AddonManager::initialize();

		RouteManager::run();
	}

}