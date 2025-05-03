<?php
// config.php – default local-dev MySQL credentials
$db_host = 'localhost';      // or '127.0.0.1'
$db_name = 'recipe_app';     // make sure you created this database
$db_user = 'root';
$db_pass = '';               // empty password by default on local setups

$dsn = "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    exit('DB Connect Error: ' . $e->getMessage());
}