<?php

require_once __DIR__ . '/../config/database.php';

class Url {
	private $db;
	
	public function __construct() {
		$this->db = getDB();
	}
	
	public function create($originalUrl, $shortCode, $userId = NULL) {
		$stmt = $this->db->prepare(
			"INSERT INTO urls (original_url, short_code, user_id) 
			 VALUES (:original_url, :short_code, :user_id)"
		);
		$stmt->execute([
			':original_url' => $originalUrl,
			':short_code'	=> $shortCode,
			':user_id'		=> $userId
		]);
		return $this->db->lastInsertId();
	}
	
	public function findByCode($shortCode) {
		$stmt = $this->db->prepare(
			"SELECT * FROM urls WHERE short_code = :short_code"
		);
		
		$stmt->execute([':short_code' => $shortCode]);
		return $stmt->fetch();
	}
	
	public function incrementClicks($urlId) {
		$stmt = $this->db->prepare(
			"UPDATE urls SET clicks = clicks + 1 WHERE id = :id"
		);
		$stmt->execute([':id' => $urlId]);
		
		$stmt = $this->db->prepare(
			"INSERT INTO clicks (url_id) VALUES (:url_id)"
		);
		$stmt->execute([':url_id' => $urlId]);
	}
	
	public function getByUser($userId) {
		$stmt = $this->db->prepare(
			"SELECT * FROM urls WHERE user_id = :user_id
			 ORDER BY created_at DESC"
		);
		$stmt->execute([':user_id' => $userId]);
		return $stmt->fetchAll();
	}
	
	public function getClicksByDay($urlId) {
		$stmt = $this->db->prepare(
			"SELECT DATE(clicked_at) as date, COUNT(*) as count
			 FROM clicks
			 WHERE url_id = :url_id
			 GROUP BY DATE(clicked_at)
			 ORDER BY date DESC
			 LIMIT 30"
		);
		$stmt->execute([':url_id' => $urlId]);
		return $stmt->fetchAll();
	}
}