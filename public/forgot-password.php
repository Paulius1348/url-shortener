<?php
$pageTitle = 'Forgot Password - URL Shortener';
require_once __DIR__ . '/partials/header.php';

if ($auth->isLoggedIn()) {
    header('Location: /dashboard');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $response = $auth->forgotPassword($email);

    if (isset($response['error'])) {
        $error = $response['error'];
    } else {
        $success = 'If this email is registered you will receive a reset link shortly.';
    }
}
?>

	<div class="container mt-5">
		<div class="row justify-content-center">
			<div class="col-md-5">
				<div class="card shadow">
					<div class="card-body p-4">
						<h2 class="text-center mb-4">Forgot Password</h2>

						<?php if ($error): ?>
							<div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
						<?php endif; ?>

						<?php if ($success): ?>
							<div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
						<?php endif; ?>

						<?php if (!$success): ?>
							<form method="POST">
								<div class="mb-3">
									<label class="form-label">Email</label>
									<input 
										type="email" 
										name="email" 
										class="form-control"
										value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
										required
									>
								</div>
								<button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
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