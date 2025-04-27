<?php
require_once "pdo.php";
require_once "utils.php";
session_start();
loginSecurity();

if (!isset($_GET['summary_id'])) {
    $_SESSION['error'] = "Missing summary_id";
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT file_path FROM summaries WHERE summary_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['summary_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row === false) {
    $_SESSION['error'] = 'Invalid summary_id';
    header('Location: index.php');
    exit();
}

$filePath = $row['file_path'];

if (!file_exists($filePath)) {
    $_SESSION['error'] = 'File not found';
    header('Location: index.php');
    exit();
}

// Get original filename (removing the unique prefix)
$originalFileName = basename($filePath);
if (preg_match('/^[0-9a-f]{13}_(.+)$/', $originalFileName, $matches)) {
    $originalFileName = $matches[1];
}

// Set headers for file download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $originalFileName . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filePath));

// Output file content
readfile($filePath);
exit();
