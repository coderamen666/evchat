<?php
session_start();
if(isset($_SESSION['name'])){
    $text = $_POST['text'];
    if($text != ""){
      if($text == "/admins"){
        foreach(file("admins.txt",FILE_IGNORE_NEW_LINES) as $admin){
          $admin_message = "<div class='msgln'><span class='chat-time'>".date("g:i A")."</span> User <b class='user-name'>". $admin ."</b> is an admin.<br></div>\n";
          file_put_contents("log.html", $admin_message, FILE_APPEND | LOCK_EX);
        }
      }elseif(0 == filesize("log.html")){
        $text_message = "<div class='msgln'><span class='chat-time'>".date("g:i A")."</span> 1 <b class='user-name'>".$_SESSION['name']."</b> ".stripslashes(htmlspecialchars($text))."<br></div>\n";
      }else{
        $text_message = "<div class='msgln'><span class='chat-time'>".date("g:i A")."</span> " . strval(count(file("log.html")) + 1) . " <b class='user-name'>".$_SESSION['name']."</b> ".stripslashes(htmlspecialchars($text))."<br></div>\n";
      }
      file_put_contents("log.html", $text_message, FILE_APPEND | LOCK_EX);
    }
}
?>