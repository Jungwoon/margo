<?php
  $hostname = "localhost";
  $username = "root";
  $password = "0000";
  $dbname = "margo";

  $output;
  $error;

  $conn = mysqli_connect($hostname, $username, $password, $dbname);
  
//  $username = $_POST['username'];
//  $event_action = $_POST['event_action'];

  $username = "test1";
  $event_action = "eos";

  $statement = "practice001.exe " . $username . " " . $event_action;
  
  // exec 명령어 알고리즘 실행시키는 부분
  exec($statement, $output, $error);
  
  echo " output : " . $output[0];
  echo " error : " . $error;

  sleep(2); // 2초간 지연

  $arr = explode(',', $output[0]);

  $query = "select url from music where id in (";

  // Query를 만들어주는 부분
  for($i=0; $i<count($arr); $i++) {
    if($i == count($arr)-1) {
      $query = $query . $arr[$i] . ");";
    }
    else {
      $query = $query . $arr[$i] . ",";
    }
  }

  $myArr = array();

  $result = mysqli_query($conn, $query);
  $temp;

  while($data=mysqli_fetch_array($result)) {
    // ex - {abc.mp3, bac.mp3};
    array_push($myArr, $data[0]);
  }

  mysqli_close($conn);

  header('Content-Type: application/json');
  echo json_encode($myArr);
?>