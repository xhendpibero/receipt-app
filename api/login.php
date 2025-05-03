<?php
// login.php
session_start();
require_once 'config.php';

// The URL of your login form (adjust as needed)
$loginUrl = '../auth-login.php';

// Helper to redirect with an error code
function redirectWithError($code) {
    global $loginUrl;
    header('Location: ' . $loginUrl . '?error=' . urlencode($code));
    exit;
}

// 1) Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectWithError('method_not_allowed');
}

// 2) Collect inputs
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = $_POST['password'] ?? '';

// 3) Validate presence
if ($username === '' || $password === '') {
    redirectWithError('empty_fields');
}

// 4) Fetch user
$stmt = $pdo->prepare('
    SELECT id, password_hash
    FROM users
    WHERE username = ?
');
$stmt->execute([$username]);
$user = $stmt->fetch();

// 5) Verify user & password
if (! $user || ! password_verify($password, $user['password_hash'])) {
    redirectWithError('invalid_credentials');
}

// 6) Success: regenerate session & redirect to dashboard
session_regenerate_id(true);
$_SESSION['user_id']  = $user['id'];
$_SESSION['username'] = $username;

header('Location: ../');
exit;