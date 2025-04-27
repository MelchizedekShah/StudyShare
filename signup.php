<?php
require_once "pdo.php";
require_once "utils.php";   
session_start();

// Initialize variables with default values
$failure = false;

// Process signup form submission
if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['pass'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['pass']);

    // Server-side validation
    $errors = [];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    }
    if (strlen($password) > 128) {
        $errors[] = "Password cannot exceed 128 characters";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter";
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter";
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number";
    }
    if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        $errors[] = "Password must contain at least one special character (e.g., !@#$%^&*)";
    }
    if (preg_match('/\s/', $password)) {
        $errors[] = "Password cannot contain spaces";
    }

    // Report all the error messages
    if (!empty($errors)) {
        $_SESSION['form_data'] = [
            'name' => $name,
            'email' => $email
        ];

        $_SESSION['error'] = implode(", ", $errors);
        header("Location: signup.php");
        exit();
    }

    // Check if email already exists
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :em');
    $stmt->execute(array(':em' => $email));
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        $failure = "Email already registered";
        $_SESSION['error'] = $failure;
        $_SESSION['form_data'] = [
            'name' => $name,
            'email' => $email,];
        header("Location: signup.php");
        exit();
    }

    // Create new user
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Generate a unique code (e.g., using md5 and timestamp)
    $uniqueCode = md5(uniqid(rand(), true));

    $stmt = $pdo->prepare('INSERT INTO users (name, email, password, unique_code, verified) VALUES (:nm, :em, :pw, :uq, :verified)');
    $stmt->execute(array(
        ':nm' => $name,
        ':em' => $email,
        ':pw' => $hashedPassword,
        ':uq' => $uniqueCode,
        ':verified' => 0
    ));

   
    $domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];

    // Verification link, that will be sent to the user, to verify their email address
    $verficationlink = "http://".$domain."/school-crud/verify_email.php?code=$uniqueCode&email=" . urlencode($email);

    // function that is in the utils.php
    $results =  mailer_sender(
        'smtp.gmail.com', 
        'studyshare02@gmail.com', 
        'StudyShare', 
        'kiao fttv yyvf ykwy', 
        $email, 
        $name, 
        $verficationlink);
    

    if ($results !== 'Verification email has been sent successfully') {
        error_log("Email sending failed for $email: $results");
        $_SESSION['error'] = $results;
        header("Location: signup.php");
        exit();
    }

    $_SESSION['success'] = $results;
    unset($_SESSION['form_data']);
    error_log("Registration success unverified: " . $email);
    header("Location: verify_page.php");
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
    <link rel="stylesheet" href="css/login_signup.css">
    <script src="js/login_signup.js"></script>
</head>
<body>

<div class="login-container">
<h2>Sign Up</h2>
<?php
flashMessages(); 
$email = '';
$name  = '';
getFormData($email, $name);
?>
<form method="POST" autocomplete="off">
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?php echo htmlentities($name); ?>" placeholder="Enter name" required autocomplete="off" aria-autocomplete="none">
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo htmlentities($email); ?>" placeholder="Enter email" required autocomplete="off" aria-autocomplete="none">
    </div>
    <div class="form-group">
            <label for="password">Password</label>
        <div class="password-container">
            <input type="password" id="password" name="pass" placeholder="Enter password" required autocomplete="new-password" aria-autocomplete="none">
            <span class="toggle-password" onclick="togglePassword()">Show</span>
        </div>
    </div>
    <div class="button-group">
        <button type="submit" onclick="return doValidate();" class="button button-signup">Sign Up</button>
        <button type="button" onclick="window.location.href='index.php'" class="button button-cancel">Cancel</button>
        <button type="button" onclick="window.location.href='login.php'" class="button button-login">Log In</button>
    </div>
</form>
</div>


</body>
</html>