<?php

session_start();

// Polyfill for PHP 4 - PHP 7, safe to utilize with PHP 8

if (!function_exists('str_contains')) {
  function str_contains (string $haystack, string $needle){
    return empty($needle) || strpos($haystack, $needle) !== false;
  }
}

function remove_line($file,$string){
	$i=0;$array=array();
	$read = fopen($file, "r") or die("can't open the file");
	while(!feof($read)) {
		$array[$i] = fgets($read);
		++$i;
	}
	fclose($read);
	$write = fopen($file, "w") or die("can't open the file");
	foreach($array as $a) {
		if(!strstr($a,$string)){
      fwrite($write,$a);
    }else{
      $delete_message = "<div class='msgln'><span class='chat-time'>".date("g:i A")."</span><span class='left-info'> User <b class='user-name-left'>". $_SESSION['name'] ."</b> has deleted this message</span><br></div>\n";
      fwrite($write, $delete_message);
    }
	}
	fclose($write);
}

if(isset($_SESSION['name'])){
  if(isset($_GET["postID"])){
    $admins = file("admins.txt", FILE_IGNORE_NEW_LINES);
    $lines_of_log_html = file("log.html");
    $the_line = $lines_of_log_html[(int) $_GET['postID'] - 1];
    if(in_array($_SESSION['name'], $admins)){
      remove_line("log.html", $the_line);
    }else{
      $name_html = "<b class='user-name'>" . $_SESSION['name'];
      if(str_contains($the_line, $name_html)){
        remove_line("log.html", $the_line);
      }
    }
  }
}
header("Location: index.php");
?>