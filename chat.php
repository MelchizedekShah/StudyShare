<?php
session_start();
require_once "pdo.php";
require_once "utils.php";

$_SESSION['summary_id'] = $_GET['summary_id'];
$id = $_GET['summary_id'];
$file_name = "chat_logs/log" . $id . ".html";
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
    <link rel="stylesheet" href="css/style_chat.css" type="text/css" />
</head>
<body>
<?php loginSecurity(); ?>
    <div id="wrapper">
        <div id="menu">
            <p class="welcome">Welcome, <b><?php echo $_SESSION['name']; ?></b></p>
            <p class="logout"><a id="exit">Exit Chat</a></p>
        </div>
        <div id="chatbox">
            <?php
            if (file_exists($file_name) && filesize($file_name) > 0) {
                $contents = file_get_contents($file_name);
                echo $contents;
            }
            ?>
        </div>
        <form name="message" action="">
            <input name="usermsg" type="text" id="usermsg" />
            <input name="submitmsg" type="submit" id="submitmsg" value="Send" />
        </form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            // Form submission
            $("form[name='message']").submit(function (e) {
                e.preventDefault();
                var clientmsg = $("#usermsg").val();
                if (clientmsg.trim() !== "") {
                    $.post("post.php", { text: clientmsg });
                    $("#usermsg").val("");
                }
                return false;
            });

            // Load chat log
            function loadLog() {
                var oldscrollHeight = $("#chatbox")[0].scrollHeight - 20;
                $.ajax({
                    url: "<?php echo $file_name; ?>",
                    cache: false,
                    success: function (html) {
                        $("#chatbox").html(html);
                        var newscrollHeight = $("#chatbox")[0].scrollHeight - 20;
                        if (newscrollHeight > oldscrollHeight) {
                            $("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log("Error loading chat log: " + error);
                    }
                });
            }

            setInterval(loadLog, 2500);

            // Exit chat
            $("#exit").click(function () {
                if (confirm("Are you sure you want to exit the chat?")) {
                    window.location = "view.php?summary_id=<?php echo $_GET['summary_id']; ?>";
                }
            });
        });
    </script>
</body>
</html>