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

$stmt = $pdo->prepare("SELECT * FROM summaries WHERE summary_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['summary_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = 'Bad value for summary_id';
    header('Location: index.php');
    return;
}

// Sanitize output
$title = htmlentities($row['title']);
$schoolYear = htmlentities($row['school_year']);
$subject = htmlentities($row['subject']);
$description = htmlentities($row['description']);
$filePath = htmlentities($row['file_path']);
// Get original filename (removing the unique prefix)
$originalFileName = basename($filePath);
if (preg_match('/^[0-9a-f]{13}_(.+)$/', $originalFileName, $matches)) {
    $originalFileName = $matches[1];
}
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
    <link rel="stylesheet" href="css/style_view.css">

</head>
<body>
<div class="container">
    <h1>File Details</h1>
    <p>Title: <strong><?=$title?></strong></p>
    <p>School Year: <strong>
        <?php
        $yearMap = [
            1 => 'Brugklas 1',
            2 => 'Brugklas 2',
            3 => 'Havo 3',
            4 => 'Vwo 3',
            5 => 'Havo 4',
            6 => 'Vwo 4',
            7 => 'Havo 5',
            8 => 'Vwo 5',
            9 => 'Vwo 6'
        ];
        echo isset($yearMap[$schoolYear]) ? $yearMap[$schoolYear] : $schoolYear;
        ?>
    </strong></p>
    <p>Subject: <strong><?=$subject?></strong></p>
    <p>Description: <strong><?=$description?></strong></p>
    <p>File: <a href="download.php?summary_id=<?=$_GET['summary_id']?>" class="download-btn">Download <?=$originalFileName?></a></p>
    <p>Chat: <a href="chat.php?summary_id=<?=$_GET['summary_id']?>" class="download-btn" style="background-color:#213155f0;">Chat</a></p>
    <p><a href="index.php">Back to List</a></p>
</div>
</body>
</html>