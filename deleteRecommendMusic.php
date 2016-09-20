<?php
  $hostname = "localhost";
  $username = "root";
  $password = "0000";
  $dbname = "margo";

  $conn = mysqli_connect($hostname, $username, $password, $dbname);
  date_default_timezone_set('Asia/Seoul');

  $user_id = $_POST['user_id'];
  
  $query = "update members set recommend_song=NULL, recommend_id=NULL where username='" . $user_id . "'";
  
  $result = mysqli_query($conn, $query);

  mysqli_close($conn);
?>