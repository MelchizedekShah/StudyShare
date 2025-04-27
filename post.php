<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['name'])) {
    $text = $_POST['text'];
    
    // Sanitize and format the message
    $text_message = "<div class='msgln'><span class='chat-time'>" . date("g:i A") . "</span> <b class='user-name'>" . $_SESSION['name'] . "</b> " . stripslashes(htmlspecialchars($text)) . "<br></div>";

    $id = $_SESSION['summary_id'];
    
    $directory = "chat_logs/"; 
    
    // Ensure the directory exists, create it if it doesn’t
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true); 
    }

    $file_path = $directory . "log" . $id . ".html";

    // Write the message to the file
    file_put_contents($file_path, $text_message, FILE_APPEND | LOCK_EX);
}
?>