<?php
   $hostname = "localhost";
   $username = "root";
   $password = "0000";
   $dbname = "margo";
   
   // 수정 전
//   $conn = mysql_connect($hostname, $username, $password) or die("Connection Fail");
//   $db = mysql_select_db($dbname, $conn) or die("db error");

   $conn = mysqli_connect($hostname, $username, $password, $dbname);

   date_default_timezone_set('Asia/Seoul');
 
   $access_day = date("Ymd");
   $username = $_POST['username'];
   $event_action = $_POST['event_action'];
   $music_title = $_POST['music_title'];
   
   if(!empty($_POST['current_nice'])) {
      $current_nice = $_POST['current_nice'];
   }
   else {
      $current_nice = '0';
   }
   
   if(!empty($_POST['duration_nice'])) {
      $duration_nice = $_POST['duration_nice'];
   }
   else {
      $duration_nice = '0';
   }

   $now_date = date("Y-m-d H:i:s");
   $username = stripslashes($username);
   $event_action = stripslashes($event_action);
   $music_title = stripslashes($music_title);
   $current_nice = stripslashes($current_nice);
   $duration_nice = stripslashes($duration_nice);
   $now_date = stripslashes($now_date);
   
   $query = sprintf("INSERT INTO events (access_day, username, event_action, music_title, current_nice, duration_nice, now_date) values('%s', '%s', '%s', '%s', '%s', '%s', '%s')",
    $access_day,
    $username,
    $event_action,
    $music_title,
    $current_nice,
    $duration_nice,
    $now_date);
   
   // 수정 전
//   $result = mysql_query($query, $conn) or die("Query Error");

   $result = mysqli_query($conn, $query);
   mysqli_close($conn);

    echo $query;
?>