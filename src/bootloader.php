<?php

/**
* @author      Software Department <developers@rovota.com>
* @copyright   Copyright (c), Rovota
* @license     MIT
*/

use Rovota\Core\Kernel\Application;
use Rovota\Core\Kernel\ExceptionHandler;

// -----------------

ob_start();
date_default_timezone_set('UTC');

// -----------------

ExceptionHandler::initialize();

set_exception_handler(function(Throwable $exception) {
   ExceptionHandler::addThrowable($exception, true);
});

set_error_handler(function(int $number, string $message, string $file, int $line) {
	if (getenv('ENABLE_DEBUG') === 'true') {
		ExceptionHandler::addError($number, $message, $file, $line);
	}
});

// -----------------

error_reporting(getenv('ENABLE_DEBUG') === 'true' ? E_ALL : 0);
ini_set('display_errors', getenv('ENABLE_DEBUG'));
ini_set('log_errors', getenv('ENABLE_LOGGING') === 'true' ? 1 : 0);

// -----------------

// Functions
require 'functions.php';

// -----------------

// Start executing the core system
/** @noinspection PhpUnhandledExceptionInspection */
Application::start();