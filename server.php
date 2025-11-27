<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// Handle subdirectory deployment (e.g., /gamma)
// Remove the subdirectory prefix from URI if present
// This allows the app to work when deployed at http://domain/gamma
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
if ($scriptDir !== '/' && $scriptDir !== '\\' && strpos($uri, $scriptDir) === 0) {
    $uri = substr($uri, strlen($scriptDir));
}
if ($uri === '') {
    $uri = '/';
}

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    return false;
}

require_once __DIR__ . '/public/index.php';
