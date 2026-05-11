<?php
$pageTitle = 'Login - URL Shortener';
require_once __DIR__ . '/partials/header.php';

if ($auth->isLoggedIn()) {
	header('Location: /dashboard');
	exit;
}

$error = '';
$success = '';

if (isset($_GET['registered'])) {
	$success = 'Registration successful! Please login.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$email 	  = trim($_POST['email'] ?? '');
	$password = trim($_POST['password'] ?? '');
	
	$response = $auth->login($email, $password);
	
	if (isset($response['error'])) {
		$error = $response['error'];
	} else {
		header('Location: /dashboard');
		exit;
	}
}

?>
	<div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4">Login</h2>

                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                        <?php endif; ?>

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
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input 
                                    type="password" 
                                    name="password" 
                                    class="form-control"
                                    required
                                >
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>

                        <p class="text-center mt-3">
                            Don't have an account? <a href="/register">Register</a>
                        </p>
						<p class="text-center">
							<a href="/forgot-password">Forgot password?</a>
						</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>