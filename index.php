<?php
require_once "pdo.php";
require_once "utils.php";
session_start();

// Prepare the base query
$query = "SELECT title, subject, school_year, summary_id FROM summaries";
$params = array();
$whereClauses = array();

// Handle search for title
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = trim($_GET['search']);
    $whereClauses[] = "(title LIKE :search OR subject LIKE :search)";
    $params[':search'] = "%$search%";
}

// Handle school year filter
if (isset($_GET['school_year']) && !empty($_GET['school_year'])) {
    $whereClauses[] = "school_year = :school_year";
    $params[':school_year'] = $_GET['school_year'];
}

// Handle subject filter
if (isset($_GET['subject']) && !empty($_GET['subject'])) {
    $whereClauses[] = "subject = :subject";
    $params[':subject'] = $_GET['subject'];
}

if (!empty($whereClauses)) {
    $query .= " WHERE " . implode(" AND ", $whereClauses);
}

// Add ORDER BY clause for better organization
$query .= " ORDER BY school_year, subject, title";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <link rel="stylesheet" href="css/style_index.css">
</head>
<body>
<div class="container">
<?php
// Check if user is logged in
if (!isset($_SESSION['name'])) {
    ?>
    <h1>StudyShare</h1>
    <p class="login-link"><a href="login.php">Please log in</a></p>
    <?php
} else {
    ?>
    <h1>StudyShare</h1>
    <?php flashMessages(); ?>

    <!-- Show links to other pages (sort of a dock) -->
    <p class="login-link"><a href="add.php">Add New File</a> | <a href="settings.php">User Settings</a> | <a href="logout.php">Logout</a></p>
<?php } ?>

    <!-- Search Form -->
    <form class="search-form" method="GET">
        <input type="text" name="search" placeholder="Search title or subject" 
               value="<?= htmlentities($_GET['search'] ?? '') ?>">
        <select name="school_year">
            <option value="">All Years</option>
            <?php
            $years = [
                1 => 'Brugklas 1', 2 => 'Brugklas 2', 3 => 'Havo 3',
                4 => 'Vwo 3', 5 => 'Havo 4', 6 => 'Vwo 4',
                7 => 'Havo 5', 8 => 'Vwo 5', 9 => 'Vwo 6'
            ];
            foreach ($years as $value => $label) {
                $selected = (isset($_GET['school_year']) && $_GET['school_year'] == $value) ? 'selected' : '';
                echo "<option value=\"$value\" $selected>$label</option>";
            }
            ?>
        </select>
        <select name="subject">
        <option value="">All Subjects</option>
            <?php
                $subjects = [
                'wiskunde' => 'Wiskunde',
                'WA' => 'Wiskunde A',
                'WB' => 'Wiskunde B',
                'WC' => 'Wiskunde C',
                'WD' => 'Wiskunde D',
                'rekenen' => 'Rekenen',
                'biologie' => 'Biologie',
                'scheikunde' => 'Scheikunde',
                'natuurkunde' => 'Natuurkunde',
                'techniek' => 'Techniek',
                'engels' => 'Engels',
                'cambridge' => 'Cambridge',
                'nederlands' => 'Nederlands',
                'papiaments' => 'Papiaments',
                'frans' => 'Frans',
                'spaans' => 'Spaans',
                'geschiedenis' => 'Geschiedenis',
                'aardrijkskunde' => 'Aardrijkskunde',
                'kunst' => 'Kunst',
                'beeldendevorming' => 'Beeldende Vorming',
                'muziek' => 'Muziek',
                'informatica' => 'Informatica',
                'economie' => 'Economie',
                'bedrijfseconomie' => 'Bedrijfs Economie',
                'maatschappijleer' => 'Maatschappijleer (asw)',
                'CAV' => 'Culturele Artistieke Vorming',
                'verzorging' => 'Verzorging',
                'godsdienst' => 'Godsdienst',
                'lo' => 'Lichamelijke Opvoeding',
                'other' => 'Other'
                ];
                foreach ($subjects as $value => $label) {
                    $selected = (isset($_GET['subject']) && $_GET['subject'] == $value) ? 'selected' : '';
                    echo "<option value=\"$value\" $selected>$label</option>";
                }
            ?>
        </select>
            <button type="submit">Search</button>
    </form>

    <?php
if ($rows === false || count($rows) == 0) {
    echo('<p class="no-summaries">No summaries found</p>');
} else {
    echo "<table>";
    echo "<tr>";
    echo "<th>Title</th>";
    echo "<th>Subject</th>";
    echo "<th>School Year</th>";
    if (isset($_SESSION['name'])) {
        echo "<th>Action</th>";
    }
    echo "</tr>";

    foreach ($rows as $row) {
        $yearDisplay = $years[$row['school_year']] ?? $row['school_year'];
        $subjectDisplay = $subjects[$row['subject']] ?? $row['subject'];

        echo "<tr>";
        if (isset($_SESSION['name'])) {
            echo "<td><a href='view.php?summary_id=" . htmlentities($row['summary_id']) . "'>" . htmlentities($row['title']) . "</a></td>";
        } else {
            echo "<td>" . htmlentities($row['title']) . "</td>";
        }
        echo "<td>" . htmlentities($subjectDisplay) . "</td>";
        echo "<td>" . htmlentities($yearDisplay) . "</td>";
        if (isset($_SESSION['name'])) {

            // Prepare the statement
            $stmt = $pdo->prepare("SELECT user_id FROM summaries WHERE summary_id = :id");
            $stmt->execute(['id' => $row['summary_id']]);
            $user_row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user_row === null) {
                echo "<td></td>";
            } elseif ($_SESSION['user_id'] === $user_row['user_id']) {
                echo "<td>";
                echo '<a href="edit.php?summary_id=' . htmlentities($row['summary_id']) . '">Edit</a> / ';
                echo '<a href="delete.php?summary_id=' . htmlentities($row['summary_id']) . '">Delete</a>';
                echo "</td>";

            } else {
                echo "<td></td>";
            }
        }
        echo "</tr>";
    }
    echo "</table>";
}
?>
</div>
</body>
</html>