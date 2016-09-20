<?php
  $hostname = "localhost";
  $username = "root";
  $password = "0000";
  $dbname = "margo";

  $conn = mysqli_connect($hostname, $username, $password, $dbname);
  date_default_timezone_set('Asia/Seoul');

  $user_id = $_POST['user_id'];
  
  $query = "select recommend_song, recommend_id from members where username='" . $user_id . "'";
  
  $result = mysqli_query($conn, $query);
  $row = mysqli_fetch_row($result);

  echo $row[0] . ','. $row[1];
  mysqli_close($conn);

?>