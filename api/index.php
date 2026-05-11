<?php
require_once __DIR__ . '/../src/controllers/UrlController.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$path = trim($_GET['path'] ?? '', '/');

switch ($path) {
	case 'urls':
		if ($method === 'GET') {
			$code = $_GET['code'] ?? '';
			if (empty($code)) {
				echo json_encode(['error' => 'Code is required']);
				exit;
			}
			$urlController = new UrlController();
			$stats = $urlController->getStats($code);
			if (!$stats) {
				echo json_encode(['error' => 'URL not found']);
				exit;
			}
			echo json_encode($stats);
		} elseif ($method == 'POST') {
			$data = json_decode(file_get_contents('php://input'), true);
			$originalUrl = $data['url'] ?? '';
			if (empty($originalUrl)) {
				echo json_encode(['error' => 'URL is required']);
				exit;
			}
			$urlController = new UrlController();
			$response = $urlController->shorten($originalUrl);
			echo json_encode($response);
		}
		break;
		
	case 'stats':
		if ($method === 'GET') {
			$urlController = new UrlController();
			$pdo = getDB();
			$stmt = $pdo->query("SELECT COUNT(*) as total_urls, SUM(clicks) as total_clicks FROM urls");
            $stats = $stmt->fetch();
			echo json_encode($stats);
		}
		break;
		
	default:
		echo json_encode(['error' => 'Endpoint not found']);
		break;
}