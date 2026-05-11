<?php
require_once __DIR__ . '/../../src/controllers/AuthController.php';
$auth = new AuthController();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'URL Shortener' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.x.x/dist/js/bootstrap.bundle.min.js"></script> 
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark navbar-expand-sm">
    <div class="container">
        <a class="navbar-brand" href="/">URL Shortener</a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <div class="ms-auto d-flex flex-column flex-sm-row gap-2 py-2 py-sm-0">
                <?php if ($auth->isLoggedIn()): ?>
                    <a href="/dashboard" class="btn btn-outline-light btn-sm">Dashboard</a>
                    <a href="/logout" class="btn btn-outline-danger btn-sm">Logout</a>
                <?php else: ?>
                    <a href="/login" class="btn btn-outline-light btn-sm">Login</a>
                    <a href="/register" class="btn btn-outline-light btn-sm">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>