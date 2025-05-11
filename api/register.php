<?php
// register.php
session_start();
require_once 'config.php';

// Change this to the URL of your signup form
$signupUrl = '../register';

// Helper to redirect with an error code
function redirectWithError($code) {
    global $signupUrl;
    header('Location: ' . $signupUrl . '?error=' . urlencode($code));
    exit;
}

// 1) Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectWithError('method_not_allowed');
}

// 2) Collect & trim inputs
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = $_POST['password'] ?? '';

// 3) Validate
if ($username === '' || strlen($username) < 3) {
    redirectWithError('username_too_short');
}
if ($password === '' || strlen($password) < 6) {
    redirectWithError('password_too_short');
}

// 4) Check for existing username
$stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
$stmt->execute([$username]);
if ($stmt->fetch()) {
    redirectWithError('username_taken');
}

// 5) Hash & insert
$password_hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('
    INSERT INTO users (username, password_hash)
    VALUES (?, ?)
');
$stmt->execute([$username, $password_hash]);
$user_id = $pdo->lastInsertId();

// 6) Log them in
session_regenerate_id(true);
$_SESSION['user_id']  = $user_id;
$_SESSION['username'] = $username;

// 7) Redirect to dashboard
header('Location: ../');
exit;