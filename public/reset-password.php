<?php
$pageTitle = 'Reset Password - URL Shortener';
require_once __DIR__ . '/partials/header.php';

if ($auth->isLoggedIn()) {
    header('Location: /dashboard');
    exit;
}

$token = $_GET['token'] ?? '';
$error = '';
$success = '';

if (empty($token)) {
    header('Location: /login');
    exit;
}

$tokenValid = $auth->validateResetToken($token);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password        = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    $response = $auth->resetPassword($token, $password, $confirmPassword);

    if (isset($response['error'])) {
        $error = $response['error'];
    } else {
        $success = 'Password reset successful! You can now login.';
    }
}
?>

	<div class="container mt-5">
		<div class="row justify-content-center">
			<div class="col-md-5">
				<div class="card shadow">
					<div class="card-body p-4">
						<h2 class="text-center mb-4">Reset Password</h2>

						<?php if (!$tokenValid): ?>
							<div class="alert alert-danger">
								This reset link has expired or already been used.
							</div>
							<p class="text-center">
								<a href="/forgot-password" class="btn btn-primary">
									Request New Reset Link
								</a>
							</p>
						<?php elseif ($success): ?>
							<div class="alert alert-success">
								<?= htmlspecialchars($success) ?>
								<a href="/login">Login here</a>
							</div>
						<?php else: ?>
							<form method="POST">
								<input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
								<div class="mb-3">
									<label class="form-label">New Password</label>
									<input 
										type="password" 
										name="password" 
										class="form-control"
										required
									>
								</div>
								<div class="mb-3">
									<label class="form-label">Confirm Password</label>
									<input 
										type="password" 
										name="confirm_password" 
										class="form-control"
										required
									>
								</div>
								<button type="submit" class="btn btn-primary w-100">Reset Password</button>
							</form>
						<?php endif; ?>

						<p class="text-center mt-3">
							<a href="/login">Back to Login</a>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>