<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'url_shortener');
define('DB_USER', 'root');
define('DB_PASS', '');

function getDB() {
	static $pdo = null;
	
	if ($pdo === null) {
		try {
			$pdo = new PDO(
				"mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
				DB_USER,
				DB_PASS
			);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			error_log("Database connection failed: " . $e->getMessage());
            http_response_code(500);
            die("Internal server error.");
		}
	}
	
	return $pdo;
}