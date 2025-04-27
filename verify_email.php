<?php
session_start();
require_once "pdo.php";
$code_get = $_GET['code'];
$email_get = $_GET['email'];

$stmt = $pdo->prepare('SELECT unique_code FROM users WHERE email = :em');
    $stmt->execute(array(':em' => $email_get));
    $code_db = $stmt->fetchColumn();

if ($code_get == $code_db) {
    $stmt = $pdo->prepare('UPDATE users SET `verified` = 1 WHERE email = :em');
    $stmt->execute(array(':em' => $email_get));
    $_SESSION['success'] = "Email verified successfully. You can now log in.";
    header("Location: login.php");
    exit();
} else {
    $_SESSION['error'] = "Invalid verification code or email.";
    header("Location: verify_page.php");
    exit();
}
?>