<?php
require_once "pdo.php";
require_once "utils.php";
session_start();


// Process login form submission
if (isset($_POST['email']) && isset($_POST['password'])) {
    session_unset();
    
    // Basic input validation
    if (strlen($_POST['email']) < 1 || strlen($_POST['password']) < 1) {
        $_SESSION['error'] = "Email and password are required";
        header("Location: login.php");
        exit();
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Enter a valid email";
        header("Location: login.php");
        exit();
    } else {
        // Get user data from database
        $stmt = $pdo->prepare('SELECT user_id, name, password FROM users WHERE email = :em');
        $stmt->execute(array(':em' => $_POST['email']));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $password = $_POST['password'];
        $hash = $row['password'];

        if ($row !== false) {
            // Verify password
            if (password_verify($password, $hash)) {
                // Check if either the algorithm or the options have changed
                if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
                    // If so, create a new hash, and replace the old one
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare('UPDATE users SET password = :pw WHERE email = :em');
                    $stmt->execute(array(':pw' => $newHash, ':em' => $_POST['email'])); 
                };

                // Check if the user is verified
                $stmt= $pdo->prepare('SELECT verified FROM users WHERE email = :em');
                $stmt->execute(array(':em' => $_POST['email']));
                $number = $stmt->fetchColumn();

                if ($number == 0) {
                    $_SESSION['error'] = "Please verify your email before logging in.";
                    header("Location: verify_page.php");
                    exit();
                }

                $_SESSION['name'] = $row['name'];
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['success'] = "Log in succesfull.";
                error_log("Login success " . $_POST['email']);
                header("Location: index.php");
                exit();
            } else {
                error_log("Login fail " . $_POST['email']);
                $_SESSION['form_data'] = [
                    'email' => $_POST['email'],
                ];
                $_SESSION['error'] = "Invalid login credentials";
                header("Location: login.php");
                exit();
            }
        } else {
            error_log("Login fail " . $_POST['email'] . " - User not found");
            $_SESSION['error'] = "Invalid login credentials";
            header("Location: login.php");
            exit();
        }
    }
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
<h2>Please log in</h2>
<?php
flashMessages();
$email = '';
$name  = '';
getFormData($email, $name);
?>
<form method="POST" autocomplete="off">
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo htmlentities($email); ?>" placeholder="Enter email" required autocomplete="off" aria-autocomplete="none">
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <div class="password-container">
            <input type="password" id="password" name="password" placeholder="Enter password" required autocomplete="new-password" aria-autocomplete="none">
            <span class="toggle-password" onclick="togglePassword()">Show</span>
        </div>
    </div>
    <div class="button-group">
        <button type="submit" onclick="return doValidate();" class="button button-login">Log In</button>
        <button type="button" onclick="window.location.href='signup.php'" class="button button-signup">Sign Up</button>
        <button type="button" onclick="window.location.href='index.php'" class="button button-cancel">Cancel</button>
    </div>
</form>
</div>
</body>
</html>