<?php
  // getUserInfo()로부터 받은 정보
  $facebook_response = file_get_contents('php://input');
  $result = json_decode(stripcslashes($facebook_response), true);

  //  name - 이름
  //  gender - 성
  //  birthday - 생일
  //  devices - 장치
  //  education - 학력
  //  hometown - 고향
  //  sports - 스포츠
  //  inspirational_people - 영감을 받은 사람
  //  music - 음악
  //  political - 정치적 성향
  //  interested_in - 관심있는 거
  //  work - 직장
  //  religion - 종교
  //  relationship_status - 연예 상태

//  $name = $result['name']; // Jungwoon Park
//  $gender = $result['gender']; // male
//  $birthday = $result['birthday']; // 03/20/1989
//  $education = $result['education']; // Array
//  $hometown = $result['hometown']['name']; // {id: "108259475871818", name: "Seoul, South Korea"}
//  $sports = $result['sports']; // [{id: "107496599279538", name: "Snowboarding"}, {id: "136273633093178", name: "Inline skating"}]
//
////  foreach($sports as $data) {
////    $test = $test . $data[name];
////  }
//
//  $inspirational_people = $result['inspirational_people']; // [{id: "109885905704014", name: "Edward VIII of the United Kingdom"}, {id: "104090722960530", name: "Steve Jobs"}, {id: "33416011787", name: "Robert Kiyosaki"}]
//  
//  /*
//  data: [{name: "Marie Digby", id: "20820606672"}, {name: "Acoustic Collabo", id: "375057652571430"}, {name: "피아노가이 여운", id: "423687271082285"}, {name: "Acoustic Cafe", id: "113720788642894"}, {name: "어반 자카파", id: "128453570524105"}, {name: "포맨", id: "113215132029230"}, {name: "바이브", id: "105214016182010"}, {name: "Maximilian Hecker", id: "103114033062231"}, {name: "Younha", id: "223628560985317"}]
//  
//  paging: {cursors: {before: "MjA4MjA2MDY2NzIZD", after: "MjIzNjI4NTYwOTg1MzE3"}}
//  */
//  $music = $result['music']['data']; // Array
//  
//  foreach($music as $data) {
//    $test = $test . $data['name'] . ' ';
//  }
//
//  $political = $result['political']; // Barack Obama ()
//  $interested_in = $result['interested_in'][0]; // ["female"] 
//  $work = $result['work']; // Array
//  $religion = $result['religion']; // 기독교()
//  $relationship_status = $result['relationship_status']; // Single
//
//
//    
//   $output;
//   $return_var;
//
//   exec("ping -c 1 google.co.kr", $output, $return_var);
//   echo '$output : ';
//   print_r($output);
//   echo '<br>';
  
  // 여기까지는 페이스북 Graph API로 받은 정보 파싱하는 부분

  $hostname = "localhost";
  $username = "root";
  $password = "0000";
  $dbname = "margo";

  $output;
  $error;

  $conn = mysqli_connect($hostname, $username, $password, $dbname);
  
  $username = $_POST['username']; // 아직 넘기는 부분은 없음
  $event_action = $_POST['event_action']; // 이벤트 타입 넘기는 부분 ex) facebook

  $statement = "practice001.exe " . $username . " " . $event_action;
  
  // exec 명령어 알고리즘 실행시키는 부분
  exec($statement, $output, $error);

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
