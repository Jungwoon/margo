<?php
//  $facebook_response = file_get_contents('php://input');
//  $result = json_decode(stripcslashes($facebook_response), true);
//
//  //  name - 이름
//  //  gender - 성
//  //  birthday - 생일
//  //  devices - 장치
//  //  education - 학력
//  //  hometown - 고향
//  //  sports - 스포츠
//  //  inspirational_people - 영감을 받은 사람
//  //  music - 음악
//  //  political - 정치적 성향
//  //  interested_in - 관심있는 거
//  //  work - 직장
//  //  religion - 종교
//  //  relationship_status - 연예 상태
//
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

  
  echo 'http://jungwoon90.cafe24.com/media/Coffee_Stains.mp3';
?>
