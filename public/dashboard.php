<?php
$pageTitle = 'Dashboard - URL Shortener';
require_once __DIR__ . '/../src/controllers/UrlController.php';
require_once __DIR__ . '/partials/header.php';

if (!$auth->isLoggedIn()) {
	header('Loaction: /login');
	exit;
}

$urlController = new UrlController;
$userId = $auth->getCurrentUserId();
$urls = $urlController->getUserUrls($userId);


?>
	<div class="container mt-5">
		<div class="row">
			<div class="col-md-12">
				<h2 class="mb-4">My URLs</h2>

				<!-- Shorten form -->
				<div class="card shadow mb-4">
					<div class="card-body">
						<?php
						$error = '';
						$result = '';
						if (session_status() === PHP_SESSION_NONE) session_start();
						if ($_SERVER['REQUEST_METHOD'] === 'POST') {
							$originalUrl = trim($_POST['url'] ?? '');
							$response = $urlController->shorten($originalUrl, $userId);

							if (isset($response['error'])) {
								$_SESSION['error'] = $response['error'];
							} else {
								$_SESSION['success'] = $response['short_url'];
								$urls = $urlController->getUserUrls($userId);
							}
							
							header('Location: /dashboard');
							exit;
						}
						
						$error = $_SESSION['error'] ?? '';
						$result = $_SESSION['success'] ?? '';
						unset($_SESSION['error'], $_SESSION['success']);
						?>

						<form method="POST">
							<div class="input-group">
								<input 
									type="text" 
									name="url" 
									class="form-control" 
									placeholder="https://example.com/very-long-url"
									value="<?= htmlspecialchars($_POST['url'] ?? '') ?>"
								>
								<button class="btn btn-primary" type="submit">Shorten</button>
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
					</div>
				</div>

				<!-- URLs table -->
				<div class="card shadow">
					<div class="card-body">
						<?php if (empty($urls)): ?>
							<p class="text-muted text-center">No URLs yet. Shorten your first URL above!</p>
						<?php else: ?>
							<div class="table-responsive">
								<table class="table table-hover">
									<thead>
										<tr>
											<th class="d-none d-md-table-cell">Original URL</th>
											<th>Short URL</th>
											<th></th>
											<th class="d-none d-md-table-cell">Clicks</th>
											<th class="d-none d-md-table-cell">Created</th>
											<th>Stats</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($urls as $url): ?>
											<tr>
												<td class="d-none d-md-table-cell">
													<a href="<?= htmlspecialchars($url['original_url']) ?>" target="_blank">
														<?= htmlspecialchars(substr($url['original_url'], 0, 40)) ?>...
													</a>
												</td>
												<td>
													<a href="https://<?= $_SERVER['HTTP_HOST'] ?>/<?= htmlspecialchars($url['short_code']) ?>" target="_blank">
														https://<?= $_SERVER['HTTP_HOST'] ?>/<?= htmlspecialchars($url['short_code']) ?>
													</a>
												</td>
												<td>
													<button 
														class="btn btn-sm btn-outline-secondary copy-btn" 
														data-url="https://<?= $_SERVER['HTTP_HOST'] ?>/<?= htmlspecialchars($url['short_code']) ?>"
														title="Copy URL"
													>
														&#128203;
													</button>
												</td>
												<td class="d-none d-sm-table-cell"><?= $url['clicks'] ?></td>
												<td class="d-none d-md-table-cell"><?= date('Y-m-d', strtotime($url['created_at'])) ?></td>
												<td>
													<a href="/stats?code=<?= htmlspecialchars($url['short_code']) ?>" class="btn btn-sm btn-outline-primary">
														View Stats
													</a>
												</td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						<?php endif; ?>
					</div>
				</div>

			</div>
		</div>
	</div>
</body>
</html>
<script>
document.body.insertAdjacentHTML('beforeend', `
    <div id="copyToast" style="
				position: fixed;
				bottom: 20px;
				right: 20px;
				z-index: 9999;
				background: #198754;
				color: white;
				padding: 12px 20px;
				border-radius: 8px;
				display: none;
				opacity: 1;"
	>
		&#10003; Link copied to clipboard!
	</div>
`);

document.querySelectorAll('.copy-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        navigator.clipboard.writeText(this.dataset.url);
        
        const toast = document.getElementById('copyToast');
        toast.style.transition = 'none';
        toast.style.opacity = '1';
        toast.style.display = 'block';
        
        setTimeout(() => {
            toast.style.transition = 'opacity 1s ease';
            toast.style.opacity = '0';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 1000);
        }, 1500);
    });
});
</script>