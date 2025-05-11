<?php
// If already logged in, skip signup
if (! empty($_SESSION['user_id'])) {
    header('Location: home');
    exit;
}

// Map error codes to messages
$errorCodes = [
    'method_not_allowed'  => 'Invalid request method.',
    'username_too_short'  => 'Username must be at least 3 characters.',
    'password_too_short'  => 'Password must be at least 6 characters.',
    'username_taken'      => 'That username is already taken.',
];

$errorMsg = '';
if (! empty($_GET['error']) && isset($errorCodes[$_GET['error']])) {
    $errorMsg = $errorCodes[$_GET['error']];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mazer Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <link rel="stylesheet" href="assets/css/pages/auth.css">
</head>

<body>
    <div id="auth">

        <div class="row h-100">
            <div class="col-lg-5 col-12">
                <div id="auth-left">
                    <div class="auth-logo">
                        <a href="index.php"><img src="assets/images/logo/logo.png" alt="Logo"></a>
                    </div>
                    <h1 class="auth-title">Sign Up</h1>
                    <p class="auth-subtitle mb-5">Input your data to register to our website.</p>

                    <form action="api/register.php" method="post">
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="text" class="form-control form-control-xl" placeholder="Username" name="username">
                            <div class="form-control-icon">
                                <i class="bi bi-person"></i>
                            </div>
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control form-control-xl"
                            placeholder="Password"
                            required
                            >
                            <div class="form-control-icon">
                            <i class="bi bi-shield-lock"></i>
                            </div>
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input
                            type="password"
                            id="confirmPassword"
                            name="confirmPassword"
                            class="form-control form-control-xl"
                            placeholder="Confirm Password"
                            required
                            >
                            <div class="form-control-icon">
                            <i class="bi bi-shield-lock"></i>
                            </div>
                            <div id="confirmPasswordError" class="text-danger mt-1" style="display:none;">
                            Passwords do not match.
                            </div>
                        </div>

                        <button type="submit"
                                class="btn btn-primary btn-block btn-lg shadow-lg mt-5">
                            Sign Up
                        </button>
                        
                        <?php if ($errorMsg): ?>
                            <p class="text-danger"><?php echo htmlspecialchars($errorMsg); ?></p>
                        <?php endif; ?>
                    </form>
                    <div class="text-center mt-5 text-lg fs-4">
                        <p class='text-gray-600'>Already have an account? <a href="login"
                                class="font-bold">Log
                                in</a>.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 d-none d-lg-block">
                <div id="auth-right">

                </div>
            </div>
        </div>

    </div>
</body>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const form  = document.getElementById('signUpForm');
  const pwd   = document.getElementById('password');
  const cpwd  = document.getElementById('confirmPassword');
  const error = document.getElementById('confirmPasswordError');

  // On form submission…
  form.addEventListener('submit', function(e) {
    if (pwd.value !== cpwd.value) {
      e.preventDefault();
      error.style.display = 'block';
      cpwd.classList.add('is-invalid');
      pwd.classList.add('is-invalid');
    }
  });

  // As user types into "Confirm Password", hide error if they now match
  cpwd.addEventListener('input', function() {
    if (pwd.value === cpwd.value) {
      error.style.display = 'none';
      cpwd.classList.remove('is-invalid');
      pwd.classList.remove('is-invalid');
    }
  });
});
</script>
</html>