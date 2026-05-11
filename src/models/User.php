<?php

require_once __DIR__ . '/../config/database.php';

class User {
	private $db;
	
	public function __construct() {
		$this->db = getDB();
	}
	
	public function create($email, $password) {
		$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
		
		$stmt = $this->db->prepare(
			"INSERT INTO users (email, password)
			 VALUES (:email, :password)"
		);
		$stmt->execute([
			':email'	=> $email,
			':password' => $hashedPassword
		]);
		return $this->db->lastInsertId();
	}
	
	public function findByEmail($email) {
		$stmt = $this->db->prepare(
			"SELECT * FROM users WHERE email = :email"
		);
		$stmt->execute([':email' => $email]);
		return $stmt->fetch();
	}
	
	public function verifyPassword($password, $hashedPassword) {
		return password_verify($password, $hashedPassword);
	}
	
	public function emailExists($email) {
		$stmt = $this->db->prepare(
			"SELECT COUNT(*) FROM users WHERE email = :email"
		);
		
		$stmt->execute([':email' => $email]);
		return $stmt->fetchColumn() > 0;
	}
	
	public function setResetToken($email, $token) {
		$stmt = $this->db->prepare(
			"UPDATE users 
			 SET reset_token = :token, reset_token_expires = NOW() + INTERVAL 1 HOUR 
			 WHERE email = :email"
		);
		$stmt->execute([
			':token'   => $token,
			':email'   => $email
		]);
	}
	
	public function findByResetToken($token) {
		$stmt = $this->db->prepare(
			"SELECT * FROM users 
			 WHERE reset_token = :token 
			 AND reset_token_expires > NOW()"
		);
		$stmt->execute([':token' => $token]);
		return $stmt->fetch();
	}
	
	public function updatePassword($email, $newPassword) {
		$hashed = password_hash($newPassword, PASSWORD_DEFAULT);
		$stmt = $this->db->prepare(
			"UPDATE users 
			 SET password = :password, reset_token = NULL, reset_token_expires = NULL 
			 WHERE email = :email"
		);
		$stmt->execute([
			':password' => $hashed,
			':email'    => $email
		]);
	}
}