<?php

require_once __DIR__ . '/../src/controllers/UrlController.php';

$code = $_GET['code'] ?? '';

if (empty($code)) {
	header('Location: /');
	exit;
}

$urlController = new UrlController();
$originalUrl = $urlController->redirect($code);

if (!$originalUrl) {
	header('Location: /');
	exit;
}

header('Location: ' . $originalUrl);
exit;