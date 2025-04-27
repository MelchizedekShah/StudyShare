<?php
require_once "pdo.php";
require_once "utils.php";
session_start();

// Assuming loginSecurity() checks if the user is logged in
loginSecurity();

// Check ownership (this replaces the isset and initial fetch checks)
checkSummaryOwnership($pdo, $_GET['summary_id'] ?? null, $_SESSION['user_id']);

if (isset($_POST['cancel'])) {
    header("Location: index.php");
    return;
}

if (isset($_POST['delete']) && isset($_POST['summary_id'])) {
    // Check ownership again for POST request (extra security)
    checkSummaryOwnership($pdo, $_POST['summary_id'], $_SESSION['user_id']);

    // Fetch the file path for the summary to be deleted
    $stmt = $pdo->prepare("SELECT file_path FROM summaries WHERE summary_id = :xyz");
    $stmt->execute(array(":xyz" => $_POST['summary_id']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Delete the file from the server if it exists
    $filePath = $row['file_path'];
    if ($filePath && file_exists($filePath)) {
        unlink($filePath);
    }

    // Delete the chat log associated with the summary
    $chatpath = "chat_logs/log" . $_POST['summary_id'] . ".html";
    if (file_exists($chatpath)) {
        unlink($chatpath);
    }

    // Delete the summary record
    $sql = "DELETE FROM summaries WHERE summary_id = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_POST['summary_id']));

    $_SESSION['success'] = 'Summary deleted';
    header('Location: index.php');
    return;
}

// Fetch summary data for confirmation (we know it exists and user owns it from checkSummaryOwnership)
$stmt = $pdo->prepare("SELECT title, summary_id FROM summaries WHERE summary_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['summary_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Melchizedek Shah">
    <meta name="description" content="A platform, tool, or initiative designed to facilitate the sharing of educational resources, knowledge, or study materials among students or learners">
    <meta name="keywords" content="Study, Share, Summaries, School, College, University">
    <meta name="robots" content="index, follow">
    <link rel="icon" type="image/x-icon" href="images/logo.svg">
    <title>StudyShare</title>
    <link rel="stylesheet" href="css/style_delete.css">
</head>
<body>
<div class="container">
    <h1>Deleting File</h1>
    <p>Title: <?= htmlentities($row['title']) ?></p>
    <form method="post">
        <div class="form-group">
            <input type="hidden" name="summary_id" value="<?= htmlentities($row['summary_id']) ?>">
            <input type="submit" value="Delete" name="delete">
            <input type="submit" value="Cancel" name="cancel">
        </div>
    </form>
</div>
</body>
</html>