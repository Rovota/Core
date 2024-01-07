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
use Rovota\Core\Convert\ConversionManager;
use Rovota\Core\Convert\Exceptions\MissingLanguageException;
use Rovota\Core\Cookie\CookieManager;
use Rovota\Core\Database\CastManager;
use Rovota\Core\Database\ConnectionManager;
use Rovota\Core\Database\Exceptions\MissingDatabaseConfigException;
use Rovota\Core\Http\Enums\StatusCode;
use Rovota\Core\Http\RequestManager;
use Rovota\Core\Http\ResponseManager;
use Rovota\Core\Http\Throttling\LimitManager;
use Rovota\Core\Kernel\Exceptions\SystemRequirementException;
use Rovota\Core\Localization\LocalizationManager;
use Rovota\Core\Logging\Exceptions\MissingChannelConfigException;
use Rovota\Core\Logging\Exceptions\UnsupportedDriverException;
use Rovota\Core\Logging\LoggingManager;
use Rovota\Core\Mail\MailManager;
use Rovota\Core\Routing\RouteManager;
use Rovota\Core\Session\Exceptions\MissingSessionConfigException;
use Rovota\Core\Session\SessionManager;
use Rovota\Core\Storage\StorageManager;
use Rovota\Core\Support\Arr;
use Rovota\Core\Support\Str;
use Rovota\Core\Support\Version;
use Rovota\Core\Validation\ValidationManager;
use Rovota\Core\Views\ViewManager;

final class Application
{

	protected const CORE_VERSION = '1.0.2+001';
	protected const CORE_MIN_PHP = '8.2.0';
	protected const CORE_REQUIRED_EXTENSIONS = ['curl', 'exif', 'fileinfo', 'mbstring', 'openssl', 'pdo', 'sodium', 'zip', 'intl'];

	protected static string $environment;

	// -----------------

	public static Registry $registry;
	public static Server $server;
	public static Version $version;

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * @throws \Rovota\Core\Session\Exceptions\UnsupportedDriverException
	 * @throws MissingLanguageException
	 * @throws MissingChannelConfigException
	 * @throws UnsupportedDriverException
	 * @throws MissingDatabaseConfigException
	 * @throws SystemRequirementException
	 * @throws MissingSessionConfigException
	 * @throws Exception
	 */
	public static function start(): void
	{
		self::$version = new Version(self::CORE_VERSION);
		self::$server = new Server();

		self::serverCompatCheck();
		self::environmentCheck();

		LoggingManager::initialize();
		CastManager::initialize();
		CacheManager::initialize();
		ConnectionManager::initialize();
		StorageManager::initialize();

		self::$registry = new Registry();

		ValidationManager::initialize();
		CookieManager::initialize();
		SessionManager::initialize();
		RequestManager::initialize();

		if (RequestManager::getRequest()->ipAllowed() === false) {
			http_response_code(403); exit;
		}

		LimitManager::initialize();
		LocalizationManager::initialize();
		ConversionManager::initialize();
		ViewManager::initialize();
		MailManager::initialize();
		ResponseManager::initialize();
		AuthManager::initialize();
		AccessManager::initialize();
		MiddlewareManager::initialize();
		RouteManager::initialize();
		AddonManager::initialize();
		FeatureManager::initialize();

		RouteManager::run();
	}

	// -----------------

	public static function getEnvironment(): string
	{
		return self::$environment;
	}

	public static function isEnvironment(array|string $name): bool
	{
		Return Arr::containsAny([self::$environment], is_array($name) ? $name : [$name]);
	}

	public static function debugEnabled(): bool
	{
		return getenv('ENABLE_DEBUG') === 'true';
	}

	public static function loggingEnabled(): bool
	{
		return getenv('ENABLE_LOGGING') === 'true';
	}

	// -----------------

	public static function quit(StatusCode $code): never
	{
		http_response_code($code->value);
		exit;
	}

	// -----------------

	/**
	 * @throws SystemRequirementException
	 */
	protected static function serverCompatCheck(): void
	{
		if (version_compare(PHP_VERSION, self::CORE_MIN_PHP, '<')) {
			throw new SystemRequirementException(sprintf('PHP %s or newer has to be installed.', self::CORE_MIN_PHP));
		}

		foreach (self::CORE_REQUIRED_EXTENSIONS as $required_extension) {
			if (!extension_loaded($required_extension)) {
				throw new SystemRequirementException(sprintf("The '%s' extension has to be installed and enabled.", $required_extension));
			}
		}
	}

	protected static function environmentCheck(): void
	{
		if (is_string(getenv('ENVIRONMENT'))) {
			self::$environment = getenv('ENVIRONMENT');
			return;
		}

		$server_name = self::$server->get('server_name');
		$server_address = self::$server->get('server_addr');
		
		// Check for development
		if (Str::startsWithAny($server_name, ['dev.', 'local.', 'sandbox.']) || Str::endsWithAny($server_name, ['.localhost', '.local'])) {
			self::$environment = 'development';
			return;
		}
		if ($server_address === '127.0.0.1' || $server_address === '::1' || $server_name === 'localhost') {
			self::$environment = 'development';
			return;
		}

		// Check for testing
		if (Str::startsWithAny($server_name, ['test.', 'qa.', 'uat.', 'acceptance.', 'integration.']) || Str::endsWithAny($server_name, ['.test', '.example'])) {
			self::$environment = 'testing';
			return;
		}

		// Check for staging
		if (Str::startsWithAny($server_name, ['stage.', 'staging.', 'prepod.'])) {
			self::$environment = 'staging';
			return;
		}

		self::$environment = 'production';
	}

}