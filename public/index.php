<?php
$pageTitle = 'URL Shotener';
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ .  '/../src/controllers/UrlController.php';

$urlController = new UrlController();

$error = '';
$result = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$originnalUrl = trim($_POST['url'] ?? '');
	$userId = $auth->isLoggedIn() ? $auth->getCurrentUserId() : null;

	$response = $urlController->shorten($originnalUrl, $userId);

	if (isset($response['error'])) {
		$error = $response['error'];
	} else {
		$result = $response['short_url'];
	}
}
?>
	<div class="container mt-5">
		<div class="row justify-content-center">
			<div class="col-md-8">
				<div class="card shadow">
					<div class="card-body p-5">
		 				<h1 class="text-center mb-4">Shorten Your URL</h1>
						
						<form method="POST">
							<div class="input-group mb-3">
								<input 
									type ="text"
									name="url"
									class="form-control from-control-lg"
									placeholder="https://example.com/very-long-url"
									value="<?= htmlspecialchars($_POST['url'] ?? '') ?>"
								>
								<button class="btn btn-primary btn-lg" type="submit">Shorten</button>
							</div>
						</form>
						
						<?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
						
						<?php if ($result): ?>
							<div class="alert alert-success">
								<strong>Your short URL:</strong>
								<a href="<?= htmlspecialchars($result) ?>" target="_blank">
                                    <?= htmlspecialchars($result) ?>
                                </a>
							</div>
						<?php endif; ?>
						
						<?php if (!$auth->isLoggedIn()): ?>
							<p class="text-center text-muted mt-3">
								<a href="/register">Register</a> to track your URLs and see statistics!
                            </p>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<div>
	</div>
</body>
</html>