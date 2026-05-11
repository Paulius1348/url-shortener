<?php

require_once __DIR__ . '/../models/Url.php';

class UrlController {
	private $urlModel;
	
	public function __construct() {
			$this->urlModel = new Url();
	}
	
	public function shorten($originalUrl, $userId = null) {
		if (empty($originalUrl)) {
			return ['error' => 'Please enter a URL'];
		}
		
		if (!filter_var($originalUrl, FILTER_VALIDATE_URL)) {
			return ['error' => 'Invalid URL'];
		}
		
		$shortCode = $this->generateShortCode();
		
		$id = $this->urlModel->create($originalUrl, $shortCode, $userId);
		
		return [
			'id' => $id,
			'original_url' 	=> $originalUrl,
			'short_code' 	=> $shortCode,
			'short_url' 	=> 'https://' . $_SERVER['HTTP_HOST'] . '/' . $shortCode
		];
	}
	
	public function redirect($shortCode) {
		$url = $this->urlModel->findByCode($shortCode);
		
		if (!$url) {
			return null;
		}
		
		$this->urlModel->incrementClicks($url['id']);
		return $url['original_url'];
	}
	
	public function getUserUrls($userId) {
		return $this->urlModel->getByUser($userId);
	}
	
	public function getStats($shortCode) {
		$url = $this->urlModel->findByCode($shortCode);
		
		if (!$url) {
			return null;
		}
		
		$clicksByDay = $this->urlModel->getClicksByDay($url['id']);
		
		return [
			'url' 			=> $url,
			'clicks_by_day' => $clicksByDay
		];
	}
	
	private function generateShortCode($length = 6) {
		$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		do {
			$code = '';
			for ($i = 0; $i < $length; $i++) {
				$code .= $characters[random_int(0, strlen($characters) - 1)];
			}
			$existing = $this->urlModel->findByCode($code);
		} while ($existing);
		
		return $code;
	}
}