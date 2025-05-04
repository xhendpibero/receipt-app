<?php
session_start();
require_once 'api/config.php';

// A simple router map: URI → handler
$routes = [
    '/recipe-app'            => 'home.php',
    '/recipe-app/'            => 'home.php',
    '/recipe-app/login'      => 'auth-login.php',
    '/recipe-app/register'   => 'auth-register.php',
    '/recipe-app/home'       => 'home.php',
    // … add your other routes …
];

// Strip query string, trailing slashes, etc.
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rtrim($path, '/') ?: '/';

if (isset($routes[$path])) {
    // found a matching page—include it
    include __DIR__ . '/' . $routes[$path];
    exit;
}

// No match → redirect to index.php (home)
header('Location: home');
exit;