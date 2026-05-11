<?php
require_once __DIR__ . '/../libs/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/SMTP.php';
require_once __DIR__ . '/../libs/PHPMailer/Exception.php';
require_once __DIR__ . '/../config/mail.php';
require_once __DIR__ . '/../models/User.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class AuthController {
	private $userModel;
	
	public function __construct() {
		$this->userModel = new User();
	}
	
	public function register($email, $password, $confirmPassword) {
		if(!filter_var($email,  FILTER_VALIDATE_EMAIL)) {
			return ['error' => 'Invalid email address'];
		}
		
		if (strlen($password) < 8) {
			return ['error' => 'Password must be at least 8 characters'];
		}
		
		if ($password !== $confirmPassword) {
			return  ['error' => 'Passwords do not match'];
		}
		
		if ($this->userModel->emailExists($email)) {
			return ['error' => 'Email already registered'];
		}
		
		$id = $this->userModel->create($email, $password);
		
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}
		$_SESSION['user_id'] = $id;
		$_SESSION['email']   = $email;
		
		return ['success' => true, 'user_id' => $id];
	}
	
	public function login($email, $password) {
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return ['error' => 'Invalid email address'];
		}
		
		$user = $this->userModel->findByEmail($email);
		
		if (!$user) {
			return ['error' => 'Invalid email or password'];
		}
		
		if (!$this->userModel->verifyPassword($password, $user['password'])) {
			return ['error' => 'Invalid email or password'];
		}
		
		session_start();
		$_SESSION['user_id'] = $user['id'];
		$_SESSION['email'] = $user['email'];
		
		return ['success' => true];
	}
	
	public function logout() {
		session_start();
		session_destroy();
		return ['success' => true];
	}
	
	public function isLoggedIn() {
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}
		return isset($_SESSION['user_id']);
	}
	
	public function getCurrentUserId() {
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}
		return $_SESSION['user_id'] ?? null;
	}
	
	public function forgotPassword($email) {
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return ['error' => 'Invalid email address'];
		}

		$user = $this->userModel->findByEmail($email);

		// Don't reveal if email exists or not
		if (!$user) {
			return ['success' => true];
		}

		// Generate token
		$token = bin2hex(random_bytes(32));
		$this->userModel->setResetToken($email, $token);

		// Send email
		$resetLink = 'https://' . $_SERVER['HTTP_HOST'] . '/reset-password?token=' . $token;

		try {
			$mail = new PHPMailer(true);
			$mail->isSMTP();
			$mail->Host       = MAIL_HOST;
			$mail->SMTPAuth   = true;
			$mail->Username   = MAIL_USERNAME;
			$mail->Password   = MAIL_PASSWORD;
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
			$mail->Port       = MAIL_PORT;

			$mail->setFrom(MAIL_FROM, MAIL_NAME);
			$mail->addAddress($email);
			$mail->Subject = 'Password Reset Request';
			$mail->Body    = "Click the link below to reset your password:\n\n" . $resetLink . "\n\nThis link expires in 1 hour.";

			$mail->send();
			} catch (Exception $e) {
			error_log('Email error: ' . $mail->ErrorInfo);
			return ['success' => true];
		}
		return ['success' => true];
	}
	
	public function resetPassword($token, $password, $confirmPassword) {
		if (strlen($password) < 8) {
			return ['error' => 'Password must be at least 8 characters'];
		}

		if ($password !== $confirmPassword) {
			return ['error' => 'Passwords do not match'];
		}

		$user = $this->userModel->findByResetToken($token);

		if (!$user) {
			return ['error' => 'Invalid or expired reset link'];
		}

		$this->userModel->updatePassword($user['email'], $password);

		return ['success' => true];
	}
	
	public function validateResetToken($token) {
		$user = $this->userModel->findByResetToken($token);
		return $user !== false;
	}
}