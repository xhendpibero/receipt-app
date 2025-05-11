<?php
session_start();
require_once 'api/config.php';

// A simple router map: URI → handler
$routes = [
    '/recipe-app'            => 'home.php',
    '/recipe-app/'           => 'home.php',
    '/recipe-app/home.php'   => 'home.php',
    '/recipe-app/login'      => 'auth-login.php',
    '/recipe-app/register'   => 'auth-register.php',
    '/recipe-app/home'       => 'home.php',
    '/recipe-app/my-recipe'  => 'my-recipe.php',
    '/recipe-app/detail'     => 'detail-recipe.php',
];

// Strip query string, trailing slashes, etc.
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rtrim($path, '/');

// If path is empty, set it to /recipe-app
if (empty($path)) {
    $path = '/recipe-app';
}

if (isset($routes[$path])) {
    // found a matching page—include it
    include __DIR__ . '/' . $routes[$path];
    exit;
} else {
    // No match → show 404 page or default to home
    include __DIR__ . '/home.php';  // Changed from redirect to include
    exit;
}