<?php
require_once "pdo.php";
require_once "utils.php";
session_start();
loginSecurity();

// Redirect if cancel is pressed
if (isset($_POST['cancel'])) {
    header("Location: index.php");
    return;
}

$failure = false;
$success = false;

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate all required fields
    if (empty($_POST['title']) || empty($_POST['schoolYear']) || empty($_POST['subject']) || 
        empty($_POST['description']) || empty($_FILES['summaryFile']['name'])) {
        
        $_SESSION['failure'] = "All fields are required";
        header("Location: add.php");
        exit();
    }

    // File processing function
    // Check utils.php for the fileProcess function
    $filePath = fileProcess($_FILES['summaryFile']);

    // Insert into database
    $stmt = $pdo->prepare('INSERT INTO summaries 
        (user_id, title, school_year, subject, description, file_path) 
        VALUES (:uid, :ti, :sy, :sub, :desc, :fp)');
    
    $stmt->execute(array(
        ':uid' => $_SESSION['user_id'],
        ':ti' => $_POST['title'],
        ':sy' => $_POST['schoolYear'],
        ':sub' => $_POST['subject'],
        ':desc' => $_POST['description'],
        ':fp' => $filePath
    ));

    $success = "Summary uploaded successfully";
    $_SESSION['success'] = $success;
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
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
        <h2>Upload Your File</h2>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="file">Select File (PDF, DOCX, TXT, Images):</label>
                <?php flashMessages();?>
                <input type="file" id="file" name="summaryFile" accept=".pdf,.docx,.txt,.jpg,.jpeg,.png,.gif" >
            </div>
            <div class="form-group">
                <label for="title">Summary Title:</label>
                <input type="text" id="title" name="title" placeholder="e.g., Biology Cell Structure Notes" >
            </div>

            <div class="form-group">
                <label for="schoolYear">School Year:</label>
                <select id="schoolYear" name="schoolYear" >
                    <option value="">Select Year</option>
                    <option value="1">Brugklas 1</option>
                    <option value="2">Brugklas 2</option>
                    <option value="3">Havo 3</option>
                    <option value="4">Vwo 3</option>
                    <option value="5">Havo 4</option>
                    <option value="6">Vwo 4</option>
                    <option value="7">Havo 5</option>
                    <option value="8">Vwo 5</option>
                    <option value="9">Vwo 6</option>
                </select>
            </div>

            <div class="form-group">
                <label for="subject">Subject:</label>
                <select id="subject" name="subject" >
                    <option value="">Select Subject</option>
                    <option value="wiskunde">Wiskunde</option>
                    <option value="WB">Wiskunde A</option>
                    <option value="WA">Wiskunde B</option>
                    <option value="WC">Wiskunde C</option>
                    <option value="WD">Wiskunde D</option>
                    <option value="rekenen">Rekenen</option>
                    <option value="biologie">Biologie</option>
                    <option value="scheikunde">Scheikunde</option>
                    <option value="natuurkunde">Natuurkunde</option>
                    <option value="techniek">Techniek</option>
                    <option value="engels">Engels</option>
                    <option value="cambridge">Cambridge</option>
                    <option value="nederlands">Nederlands</option>
                    <option value="papiaments">Papiaments</option>
                    <option value="frans">Frans</option>
                    <option value="spaans">Spaans</option>
                    <option value="geschiedenis">Geschiedenis</option>
                    <option value="aardrijkskunde">Aardrijkskunde</option>
                    <option value="kunst">Kunst</option>
                    <option value="beeldendevorming">Beeldende Vorming</option>
                    <option value="muziek">Muziek</option>
                    <option value="informatica">Informatica</option>
                    <option value="economie">Economie</option>
                    <option value="bedrijfseconomie">Bedrijfs Economie</option>
                    <option value="maatschappijleer">Maatschappijleer (asw)</option>
                    <option value="CAV">Culturele Artestieke Vorming</option>
                    <option value="verzorging">Verzorging</option>
                    <option value="godsdienst">Godsdienst</option>
                    <option value="lo">lichamelijke opvoeding</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4" placeholder="Briefly describe the file content..." ></textarea>
            </div>

            <div class="form-group">
                <input class="upload-button" type="submit" value="Upload" name="upload">
                <input class="cancel-button" type="submit" value="Cancel" name="cancel">
            </div>
        </form>
    </div>
</body>
</html>