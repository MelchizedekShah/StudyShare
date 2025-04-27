<?php
require_once "pdo.php";
require_once "utils.php";
session_start();

// Assuming loginSecurity() checks if the user is logged in
loginSecurity();

// Check ownership (this replaces the isset and fetch checks)
checkSummaryOwnership($pdo, $_GET['summary_id'] ?? null, $_SESSION['user_id']);

// Redirect if cancel is pressed
if (isset($_POST['cancel'])) {
    header("Location: index.php");
    exit();
}

// Handle form submission
if (isset($_POST["title"]) && isset($_POST['school_year']) && isset($_POST["subject"]) && isset($_POST['description'])) {
    // Check ownership again for POST request (extra security)
    checkSummaryOwnership($pdo, $_POST['summary_id'], $_SESSION['user_id']);

    // Check if all fields are filled (file is optional for edit)
    if (strlen($_POST["title"]) < 1 || strlen($_POST["school_year"]) < 1 || strlen($_POST["subject"]) < 1 || strlen($_POST["description"]) < 1) {
        $_SESSION['failure'] = "All fields are required";
        $_SESSION['form_data'] = $_POST;
        header("Location: edit.php?summary_id=" . $_POST['summary_id']);
        exit();
    }

    try {
        // Handle file upload if a new file is provided
        $filePath = null;

        if (isset($_FILES['summaryFile']) && $_FILES['summaryFile']['error'] === UPLOAD_ERR_OK) {
            $filePath = fileProcess($_FILES['summaryFile']); 

            // Delete old file if a new one is uploaded
            $stmt = $pdo->prepare("SELECT file_path FROM summaries WHERE summary_id = :summary_id");
            $stmt->execute([':summary_id' => $_POST['summary_id']]);
            $oldFilePath = $stmt->fetchColumn();
            if ($oldFilePath && file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
        }

        // Update summary
        $sql = "UPDATE summaries SET title = :ti, school_year = :sy, subject = :sub, description = :desc";
        if ($filePath) {
            $sql .= ", file_path = :fp";
        }
        $sql .= " WHERE summary_id = :summary_id";
        
        $stmt = $pdo->prepare($sql);
        $params = array(
            ':ti' => $_POST['title'],
            ':sy' => $_POST['school_year'],
            ':sub' => $_POST['subject'],
            ':desc' => $_POST['description'],
            ':summary_id' => $_POST['summary_id']
        );
        if ($filePath) {
            $params[':fp'] = $filePath;
        }
        $stmt->execute($params);

        $_SESSION['success'] = 'Summary updated';
        header('Location: index.php');
        return;
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: index.php");
        exit();
    }
}

// Fetch summary data (we know it exists and user owns it from checkSummaryOwnership)
$stmt = $pdo->prepare("SELECT * FROM summaries WHERE summary_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['summary_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Set form variables (from session if validation failed, otherwise from database)
if (isset($_SESSION['form_data'])) {
    $ti = htmlentities($_SESSION['form_data']['title']);
    $sy = htmlentities($_SESSION['form_data']['school_year']);
    $sub = htmlentities($_SESSION['form_data']['subject']);
    $desc = htmlentities($_SESSION['form_data']['description']);
    $summary_id = $_SESSION['form_data']['summary_id'];
    unset($_SESSION['form_data']);
} else {
    $ti = htmlentities($row['title']);
    $sy = htmlentities($row['school_year']);
    $sub = htmlentities($row['subject']);
    $desc = htmlentities($row['description']);
    $summary_id = $row['summary_id'];
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
    <link rel="stylesheet" href="css/style_add.css">
</head>
<body>
<div class="upload-container">
    <?php
    if (isset($_SESSION['name'])) {
        echo "<h2>Editing for ";
        echo htmlentities($_SESSION['name']);
        echo "</h2>\n";
    }
    flashMessages();
    ?>
   <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="summary_id" value="<?= htmlspecialchars($summary_id) ?>">

    <div class="form-group">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($ti) ?>">
    </div>

    <div class="form-group">
        <label for="school_year">School Year:</label>
        <select id="school_year" name="school_year">
            <option value="1" <?= $sy == '1' ? 'selected' : '' ?>>Brugklas 1</option>
            <option value="2" <?= $sy == '2' ? 'selected' : '' ?>>Brugklas 2</option>
            <option value="3" <?= $sy == '3' ? 'selected' : '' ?>>Havo 3</option>
            <option value="4" <?= $sy == '4' ? 'selected' : '' ?>>Vwo 3</option>
            <option value="5" <?= $sy == '5' ? 'selected' : '' ?>>Havo 4</option>
            <option value="6" <?= $sy == '6' ? 'selected' : '' ?>>Vwo 4</option>
            <option value="7" <?= $sy == '7' ? 'selected' : '' ?>>Havo 5</option>
            <option value="8" <?= $sy == '8' ? 'selected' : '' ?>>Vwo 5</option>
            <option value="9" <?= $sy == '9' ? 'selected' : '' ?>>Vwo 6</option>
        </select>
    </div>

    <div class="form-group">
    <label for="subject">Subject:</label>
    <select id="subject" name="subject">
        <option value="">Select Subject</option>
        <option value="wiskunde" <?= $sub == 'wiskunde' ? 'selected' : '' ?>>Wiskunde</option>
        <option value="WA" <?= $sub == 'WA' ? 'selected' : '' ?>>Wiskunde A</option>
        <option value="WB" <?= $sub == 'WB' ? 'selected' : '' ?>>Wiskunde B</option>
        <option value="WC" <?= $sub == 'WC' ? 'selected' : '' ?>>Wiskunde C</option>
        <option value="WD" <?= $sub == 'WD' ? 'selected' : '' ?>>Wiskunde D</option>
        <option value="rekenen" <?= $sub == 'rekenen' ? 'selected' : '' ?>>Rekenen</option>
        <option value="biologie" <?= $sub == 'biologie' ? 'selected' : '' ?>>Biologie</option>
        <option value="scheikunde" <?= $sub == 'scheikunde' ? 'selected' : '' ?>>Scheikunde</option>
        <option value="natuurkunde" <?= $sub == 'natuurkunde' ? 'selected' : '' ?>>Natuurkunde</option>
        <option value="techniek" <?= $sub == 'techniek' ? 'selected' : '' ?>>Techniek</option>
        <option value="engels" <?= $sub == 'engels' ? 'selected' : '' ?>>Engels</option>
        <option value="cambridge" <?= $sub == 'cambridge' ? 'selected' : '' ?>>Cambridge</option>
        <option value="nederlands" <?= $sub == 'nederlands' ? 'selected' : '' ?>>Nederlands</option>
        <option value="papiaments" <?= $sub == 'papiaments' ? 'selected' : '' ?>>Papiaments</option>
        <option value="frans" <?= $sub == 'frans' ? 'selected' : '' ?>>Frans</option>
        <option value="spaans" <?= $sub == 'spaans' ? 'selected' : '' ?>>Spaans</option>
        <option value="geschiedenis" <?= $sub == 'geschiedenis' ? 'selected' : '' ?>>Geschiedenis</option>
        <option value="aardrijkskunde" <?= $sub == 'aardrijkskunde' ? 'selected' : '' ?>>Aardrijkskunde</option>
        <option value="kunst" <?= $sub == 'kunst' ? 'selected' : '' ?>>Kunst</option>
        <option value="beeldendevorming" <?= $sub == 'beeldendevorming' ? 'selected' : '' ?>>Beeldende Vorming</option>
        <option value="muziek" <?= $sub == 'muziek' ? 'selected' : '' ?>>Muziek</option>
        <option value="informatica" <?= $sub == 'informatica' ? 'selected' : '' ?>>Informatica</option>
        <option value="economie" <?= $sub == 'economie' ? 'selected' : '' ?>>Economie</option>
        <option value="bedrijfseconomie" <?= $sub == 'bedrijfseconomie' ? 'selected' : '' ?>>Bedrijfs Economie</option>
        <option value="maatschappijleer" <?= $sub == 'maatschappijleer' ? 'selected' : '' ?>>Maatschappijleer (asw)</option>
        <option value="CAV" <?= $sub == 'CAV' ? 'selected' : '' ?>>Culturele Artistieke Vorming</option>
        <option value="verzorging" <?= $sub == 'verzorging' ? 'selected' : '' ?>>Verzorging</option>
        <option value="godsdienst" <?= $sub == 'godsdienst' ? 'selected' : '' ?>>Godsdienst</option>
        <option value="lo" <?= $sub == 'lo' ? 'selected' : '' ?>>Lichamelijke Opvoeding</option>
        <option value="other" <?= $sub == 'other' ? 'selected' : '' ?>>Other</option>
    </select>
</div>

    <div class="form-group">
        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="4"><?= htmlspecialchars($desc) ?></textarea>
    </div>

    <div class="form-group">
        <label for="summaryFile">Upload New File (optional):</label>
        <input type="file" id="summaryFile" name="summaryFile" accept=".pdf,.docx,.txt">
    </div>

    <div class="form-group">
        <input type="submit" name="save" value="Save">
        <input type="submit" name="cancel" value="Cancel">
    </div>
</form>
</div>
</body>
</html>