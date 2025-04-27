<?php
require_once "pdo.php";
require_once "utils.php";
session_start();

// Checks if the user is logged in
loginSecurity();

// Redirect if cancel is pressed
if (isset($_POST['cancel'])) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['delete'])) {

    // Delete the user's summaries (this is alpa version)
    //This is bad!!!
    $stmt = $pdo->prepare("DELETE FROM summaries WHERE user_id = :user_id");
    $stmt->execute(array(':user_id' => $_SESSION['user_id']));

    // Delete the user account
    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = :user_id");
    $stmt->execute(array(':user_id' => $_SESSION['user_id']));
    // Clear session and redirect to login page
    session_destroy();
    header("Location: index.php");
    exit();
}

// Handle form submission
if (isset($_POST["name"])) {

    // Check if all fields are filled (file is optional for edit)
    if (strlen($_POST["name"]) < 1) {
        $_SESSION['failure'] = "All fields are required";
        header("Location: settings.php");
        exit();
    }

    // Update name in database
    try {
        $stmt = $pdo->prepare('UPDATE users SET name = :nm WHERE user_id = :user_id');
        $stmt->execute(array(
            ':nm' => trim($_POST['name']),
            ':user_id' => $_SESSION['user_id']
        ));
        $_SESSION['name'] = trim($_POST['name']); 
        $_SESSION['success'] = 'Name Updated';
        header('Location: index.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['failure'] = "Database error: " . $e->getMessage();
        header("Location: settings.php");
        exit();
    }
}

// Fetch summary data for the logged-in user
$stmt = $pdo->prepare("SELECT name FROM users WHERE user_id = :xyz");
$stmt->execute(array(":xyz" => $_SESSION['user_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$name = htmlentities($row['name']);

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
    <link rel="stylesheet" href="css/style_add.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="upload-container">
    <?php
    if (isset($_SESSION['name'])) {
        echo "<h1>Settings for ";
        echo htmlentities($_SESSION['name']);
        echo "</h1>\n";
    }
    flashMessages();
    ?>
    <form method="post">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>">
        </div>

        <div class="form-group">
            <input type="submit" name="save" value="Save">
            <input type="submit" name="delete" id="deleteAccount" value="Delete Account">
            <input type="submit" name="cancel" value="Cancel">
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    $("#deleteAccount").click(function(event) {
        if (!confirm("Are you sure you want to delete your account? This action cannot be undone.")) {
            event.preventDefault(); // Prevent form submission if user cancels
        }
    });
});
</script>
</body>
</html>