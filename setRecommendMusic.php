<?php
  $hostname = "localhost";
  $username = "root";
  $password = "0000";
  $dbname = "margo";

  $friend_id = $_POST['friend_id'];
  $user_id = $_POST['user_id'];
  $recommend_song = $_POST['recommend_song'];

  $conn = mysqli_connect($hostname, $username, $password, $dbname);

  date_default_timezone_set('Asia/Seoul');
  
  // 아이디가 존재하는지 검증하는 부분
  $verifyQuery = "select username from members where username='" . $friend_id . "'";
  $verifyResult = mysqli_query($conn, $verifyQuery);
  $verifyId;

  $data=mysqli_fetch_row($verifyResult); 
  $verifyId = $data[0];
  
  if(empty($verifyId)) {
    echo $friend_id . ' is not exist';
  }
  else {
    $query = "update members set recommend_song='".$recommend_song."', recommend_id='".$user_id."' where username='".$friend_id."'";
    $result = mysqli_query($conn, $query);
    echo 'Recommend to ' . $friend_id;
  }

  mysqli_close($conn);

?>