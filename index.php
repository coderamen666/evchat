<?php
// Made by Evan Baisch and Jacob Rogers
session_start();
if(isset($_GET['logout'])){    
    //Simple exit message
    $logout_message = "<div class='msgln'><span class='chat-time'>".date("g:i A")."</span><span class='left-info'> User <b class='user-name-left'>". $_SESSION['name'] ."</b> has left the chat session.</span><br></div>\n";
    file_put_contents("log.html", $logout_message, FILE_APPEND | LOCK_EX);
    session_destroy();
    header("Location: index.php"); //Redirect the user
}
if(isset($_POST['enter'])){
    if($_POST['name'] != "" && !in_array($_POST['name'], file("blocked.txt", FILE_IGNORE_NEW_LINES))){
        $_SESSION['name'] = stripslashes(htmlspecialchars($_POST['name']));
        $enter_message = "<div class='msgln'><span class='chat-time'>".date("g:i A")."</span><span class='left-info'> User <b class='user-name-left'>". $_SESSION['name'] ."</b> has entered the chat session.</span><br></div>\n";
        file_put_contents("log.html", $enter_message, FILE_APPEND | LOCK_EX);
    }
    else{
        echo '<span class="error">Sorry, Username Empty or Invalid :( </span>';
    }
}
function loginForm(){
    echo
    '<div id="loginform">
    <p>Please enter your name to continue!</p>
    <form action="index.php" method="post">
      <label for="name">Name &mdash;</label>
      <input type="text" name="name" id="name" />
      <input type="submit" name="enter" id="enter" value="Enter" />
    </form>
  </div>';
}
?>
 
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>EvChat!</title>
        <meta name="description" content="EvChat!" />
        <link rel="stylesheet" href="style.css" />
    </head>
    <body>
    <?php
    if(!isset($_SESSION['name'])){
        loginForm();
    }
    else {
    ?>
        <div id="wrapper">
            <div id="menu">
                <p class="welcome">Welcome, <b><?php echo $_SESSION['name']; ?></b></p>
                <p class="logout"><a id="exit" href="#">Exit Chat</a></p>
            </div>
 
            <div id="chatbox">
            <?php
            if(file_exists("log.html") && filesize("log.html") > 0){
                $contents = file_get_contents("log.html");          
                echo $contents;
            }
            ?>
            </div>
 
            <form name="message" action="">
                <input name="usermsg" type="text" id="usermsg" />
                <input name="submitmsg" type="submit" id="submitmsg" value="Send" />
            </form>
            <a href="transcript.php" id="transcript" download="transcript.html">Download Transcript</a><input type="checkbox" id="sound_enable">Enable Notification Beep</input><br/>
            <input name="line_del" type="number" id="line_numinp" step=1/>  
            <a id="delete" href="#">Delete Chat</a>
        </div>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script type="text/javascript">
        // Taken From Stack Overflow. I am shameless about my laziness!
        var audioCtx = new (window.AudioContext || window.webkitAudioContext || window.audioContext);
        
        //All arguments are optional:
        
        //duration of the tone in milliseconds. Default is 500
        //frequency of the tone in hertz. default is 440
        //volume of the tone. Default is 1, off is 0.
        //type of tone. Possible values are sine, square, sawtooth, triangle, and custom. Default is sine.
        //callback to use on end of tone
        function beep(duration, frequency, volume, type, callback) {
            var oscillator = audioCtx.createOscillator();
            var gainNode = audioCtx.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioCtx.destination);
            
            if (volume){gainNode.gain.value = volume;}
            if (frequency){oscillator.frequency.value = frequency;}
            if (type){oscillator.type = type;}
            if (callback){oscillator.onended = callback;}
            
            oscillator.start(audioCtx.currentTime);
            oscillator.stop(audioCtx.currentTime + ((duration || 500) / 1000));
        };
        </script>
        <script type="text/javascript">
            // jQuery Document
            $(document).ready(function () {
                $("#submitmsg").click(function () {
                    var clientmsg = $("#usermsg").val();
                    $.post("post.php", { text: clientmsg });
                    $("#usermsg").val("");
                    return false;
                });
                function loadLog() {
                    var oldtext = document.getElementsByTagName("html")[0].innerHTML;
                    var oldscrollHeight = $("#chatbox")[0].scrollHeight - 20; //Scroll height before the request
                    $.ajax({
                        url: "log.html",
                        cache: false,
                        success: function (html) {
                            $("#chatbox").html(html); //Insert chat log into the #chatbox div
                            //Auto-scroll           
                            var newscrollHeight = $("#chatbox")[0].scrollHeight - 20; //Scroll height after the request
                            if(newscrollHeight > oldscrollHeight){
                                $("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal'); //Autoscroll to bottom of div
                            }   
                            var newtext = document.getElementsByTagName("html")[0].innerHTML;
                            if((oldtext != newtext) && document.getElementById("sound_enable").checked){
                              beep(250);
                            }
                        }
                    });
                }
                setInterval (loadLog, 2500);
                $("#exit").click(function () {
                    var exit = confirm("Are you sure you want to end the session?");
                    if (exit == true) {
                    window.location = "index.php?logout=true";
                    }
                });
                $("#delete").click(function(){
                  var _del = confirm("Are you sure you want to rewrite history?");
                  if(_del == true){
                    window.location = "deletepost.php?postID=" + document.getElementById("line_numinp").value;
                  }
                });
            });
        </script>
    </body>
</html>
<?php
}
?>
