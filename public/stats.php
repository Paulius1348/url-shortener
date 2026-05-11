<?php
$pageTitle = 'Stats - URL Shortener';
require_once __DIR__ . '/../src/controllers/UrlController.php';
require_once __DIR__ . '/partials/header.php';

if (!$auth->isLoggedIn()) {
	header('Loaction: /login');
	exit;
}

$code = $_GET['code'] ?? '';

if (empty($code)) {
    header('Location: /dashboard');
    exit;
}

$urlController = new UrlController;
$stats = $urlController->getStats($code);

if (!$stats) {
    header('Location: /dashboard');
    exit;
}

$url = $stats['url'];
$clicksByDay = $stats['clicks_by_day'];


?>

	<div class="container mt-5">
		<div class="row justify-content-center">
			<div class="col-md-8">

				<a href="/dashboard" class="btn btn-outline-secondary mb-4">Back to Dashboard</a>

				<div class="card shadow mb-4">
					<div class="card-body">
						<h4 class="mb-3">URL Statistics</h4>
						<table class="table">
							<tr>
								<th>Original URL</th>
								<td>
									<a href="<?= htmlspecialchars($url['original_url']) ?>" target="_blank">
										<?= htmlspecialchars($url['original_url']) ?>
									</a>
								</td>
							</tr>
							<tr>
								<th>Short URL</th>
								<td>
									<a href="https://<?= $_SERVER['HTTP_HOST'] ?>/<?= htmlspecialchars($url['short_code']) ?>" target="_blank">
										https://<?= $_SERVER['HTTP_HOST'] ?>/<?= htmlspecialchars($url['short_code']) ?>
									</a>
								</td>
							</tr>
							<tr>
								<th>Total Clicks</th>
								<td><?= $url['clicks'] ?></td>
							</tr>
							<tr>
								<th>Created</th>
								<td><?= date('Y-m-d', strtotime($url['created_at'])) ?></td>
							</tr>
						</table>
					</div>
				</div>

				<div class="card shadow">
					<div class="card-body">
						<h4 class="mb-3">Clicks by Day</h4>
						<?php if (empty($clicksByDay)): ?>
							<p class="text-muted text-center">No clicks yet.</p>
						<?php else: ?>
							<table class="table table-hover">
								<thead>
									<tr>
										<th>Date</th>
										<th>Clicks</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($clicksByDay as $day): ?>
										<tr>
											<td><?= htmlspecialchars($day['date']) ?></td>
											<td><?= $day['count'] ?></td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php endif; ?>
					</div>
				</div>

			</div>
		</div>
	</div>
</body>
</html>