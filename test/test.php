<?php
  $hostname = "localhost";
  $username = "root";
  $password = "0000";
  $dbname = "margo";

  $output;
  $return_var;

  $conn = mysqli_connect($hostname, $username, $password, $dbname);

  exec("algorithm/practice001.exe 1 eos", $output, $return_var);
  
  $arr = explode(',', $output[0]);

  $query = "select url from music where id in (";

  for($i=0; $i<count($arr); $i++) {
    if($i == count($arr)-1) {
      $query = $query . $arr[$i] . ");";
    }
    else {
      $query = $query . $arr[$i] . ",";
    }
  }
  
//  echo($query . "</br>"); // for test

  $result = mysqli_query($conn, $query);

  while($data=mysqli_fetch_array($result)) {
    echo($data[0] . "</br>");
  }
  

  mysqli_close($conn);

?>