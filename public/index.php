<?php

if(!defined("APPLICATION_ROOT")) {
    define("APPLICATION_ROOT", dirname(__DIR__));
}

if(!defined("APPLICATION_ENV")) {
    $env = isset($_ENV['APPLICATION_ENV']) ? $_ENV['APPLICATION_ENV'] : isset($_SERVER['APPLICATION_ENV']) ? $_SERVER['APPLICATION_ENV'] : 'development';
    define("APPLICATION_ENV", $env);
}

if (APPLICATION_ENV == 'development') {
	error_reporting(E_ALL);
	ini_set("display_errors", 1);
}

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
