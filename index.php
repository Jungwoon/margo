<?php require "login/loginheader.php"; ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>margo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap -->
    <link href="css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="css/main.css" rel="stylesheet" media="screen">
    
    <!-- Wimpy Engine -->
    <script src="wimpy/wimpy.js"></script>
    
    <!-- jQuery CDN -->
    <script src="http://cdn.jsdelivr.net/jquery/3.1.0/jquery.min.js"></script>
    
  </head>
  
  <body>
  <div class="container">
  
  <!-- 노래 추천 Modal -->
  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
       <div class="modal-body">
        <div class="form-group">
          <label>Friend ID</label>
          <input type="text" class="form-control" id="friend_id" name="friend_id" placeholder="ID">
        </div>
       </div>

      <div class="modal-footer">
        <button name="submit" class="btn btn-primary" onclick="sendRecommend()">Recommend</button>
      </div>

      </div>
    </div>
  </div>
  
  <!-- 추천음악이 있는 경우 들을건지 말건지 확인하는 부분 -->
  <div class="modal fade" id="checkModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
       <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Alert Recommend Music</h4>
      </div>
       <div class="modal-body">
         <!-- 자바 스크립트 단에서 메시지를 넣어주는 부분 -->
         <p id="alert_contents"></p> 
       </div>

      <div class="modal-footer">
        <button name="btnConfirm" class="btn btn-default" onclick="checkRecommendYes()">Yes</button>
        <button name="btnCancel" class="btn btn-danger" onclick="checkRecommendNo()">No</button>
      </div>

      </div>
    </div>
  </div>
   
  <div class="form-signin">
  <!-- Wimpy Player -->
  <div id="myPlayer" data-skin="/wimpy/wimpy.skins/new_margo.tsv" style="display:block" data-startUpText="Loading..." data-wimpyplayer></div>
     
      <!-- Facebook Button scope에는 필요한 권한 범위를 설정해줘야 한다. -->
<!--
      <fb:login-button id="facebookButton" scope="
        public_profile,
        user_birthday,
        user_work_history,
        user_education_history,
        user_hometown,
        user_actions.music,
        user_about_me,
        user_religion_politics,
        user_relationships,
        user_relationship_details,
        user_location,
        user_events,
        user_likes" onlogin="checkLoginState();" data-size="xlarge" style="display:block">
      </fb:login-button>
-->

      <div id="status"></div> 
    <button type="button" class="btn btn-primary btn-lg btn-block" data-toggle="modal" data-target="#myModal">Recommend this music</button>
    <a href="login/logout.php" class="btn btn-default btn-lg btn-block">Sign out</a>
    
    </div> <!-- /form-signin -->
  </div> <!-- /container --> 
   
  <script>
    
    // 전역변수로 선언
    var myPlayerObject;
    var playerInfo;
    var duration_nice; // 음악 전체시간
    var current_nice; // 현재 재생시간
    var skipCount = 0;
    var current_music; // 현재음악(음악 추천을 위한 부분)
    var recommend_music_playing = false; 
    
    // 2016.08.28.
    // start of 추천 음악 관련된 부분 --------------------------------------------------------------------
    
    var recommend_music;
    var recommend_friend;
    
    // 음악 추천 해주는 부분
    function sendRecommend() {
      console.log("sendRecommend()");
      var friend_id = document.getElementById("friend_id").value;
      
      // 세션으로부터 로그인 정보 받는 부분
      var username = '<?php echo $_SESSION['username'] ?>';
      
      if (friend_id == "") {
        alert("Please fill out the form");
      }
      else {
        $.ajax({
          type: "post",
          url: "setRecommendMusic.php",
          data: {
            user_id: username,
            recommend_song: current_music,
            friend_id: document.getElementById("friend_id").value // 입력한 값을 가져와서 넘기는 부분
          },
          success: function(result) {
            console.log("sendRecommend Success : " + result);
            alert(result);
          },
          error: function(err) {
            console.log(err);
          }
        });
        
        $('#myModal').modal('hide');
        document.getElementById("friend_id").value = ""; // 버튼을 누르면 비워주는 부분
      }
    }
    
    // 추천받은 음악이 있는지 확인하는 부분
    function getRecommend() {
      console.log("getRecommend()");
      
      // 세션으로부터 로그인 정보 받는 부분
      var username = '<?php echo $_SESSION['username'] ?>';
      
      $.ajax({
          type: "post",
          url: "getRecommendMusic.php",
          data: {
            user_id: username
          },
          async: false, // 동기화 처리
          dataType: "text",
          success: function(result) {
            var values = result.split(",");
            console.log("getRecommend Success URL : " + values[0]);
            console.log("getRecommend Success ID : " + values[1]);
            
            // 추천 음악의 값이 있으면
            if(values[0] != null && values[0] != ""
              && values[1] != null && values[1] != "") {
              
              recommend_music = values[0]; // 추천받은 음악의 경로
              recommend_friend = values[1]; // 추천해준 친구 아이디
              
              $('#checkModal').modal('show');
              document.getElementById('alert_contents').innerHTML = "You have a recommend music from " + "'" + recommend_friend + "'"; 
            }
            // 추천 음악의 값이 없으면
            else {
              var username = '<?php echo $_SESSION['username'] ?>';
              getMusic(username, 'login');
            }
          },
          error: function(err) {
            console.log(err);
          }
        });
    }
    
    // 추천받은 사람에게 알려주고 NULL로 변경
    function deleteRecommend() {
      console.log("deleteRecommend()");
      
      // 세션으로부터 로그인 정보 받는 부분
      var username = '<?php echo $_SESSION['username'] ?>';
      
      $.ajax({
          type: "post",
          url: "deleteRecommendMusic.php",
          data: {
            user_id: username
          },
          success: function(result) {
            console.log("getRecommend Success");
          },
          error: function(err) {
            console.log(err);
          }
        });
    }
    
    // 추천음악 Yes 버튼을 눌렀을 경우
    function checkRecommendYes() {
      console.log("checkRecommendYes()");
      console.log("checkRecommendYes() song : " + recommend_music);
      recommend_music_playing = true; // 다음 리스트를 받기 위한 부분
      console.log("checkRecommendYes() recommend_music_playing : " + recommend_music_playing);
      
      myPlayerObject.setPlaylist(recommend_music, false);
      myPlayerObject.setInfo("Press Play"); // 디스플레이에 나오는 정보
      $('#checkModal').modal('hide');
      deleteRecommend(); // 지워주는 부분
      
    }
    
    // 추천음악 No 버튼을 눌렀을 경우
    function checkRecommendNo() {
      console.log("checkRecommendNo()");
      $('#checkModal').modal('hide');
      
      // No를 눌렀을 경우에는 'longin'으로 처리한다.
      var username = '<?php echo $_SESSION['username'] ?>';
      getMusic(username, 'login');
      deleteRecommend(); // 지워주는 부분
    }
    
    // end of 추천 음악 관련된 부분 --------------------------------------------------------------------
    
    callMediaPlayer();
    
    // Facebook 연동 관련된 부분 ----------------------------------------------------------------------
    
//    // This is called with the results from from FB.getLoginStatus().
//    function statusChangeCallback(response) {
//      var facebookButton = document.getElementById('facebookButton'); // Facebook Login 객체 받는 부분
//      
//      callMediaPlayer();
//      
//      if (response.status === 'connected') {
//        console.log("facebook connected");
//        
//        // Facebook Login 버튼 사라지게 하는 부분
//        if(facebookButton.style.display=="block") {
//          facebookButton.style.display = "none";
//        }
//        
//        // 페이스북이 정상적으로 로그인이 되면 이리로 들어오게 됨
//        getUserInfo();
//      }
//      else if (response.status === 'not_authorized') {
//        // 로그인은 했는데 권한 동의를 아직 하지 않은 경우
//        console.log("facebook not_authorized");
//        
//        // Facebook Login 버튼 나타나게 하는 부분
//        if(facebookButton.style.display=="none") {
//          facebookButton.style.display = "block";
//        }
//      }
//      else {
//        console.log("facebook no login");
//        
//        // Facebook Login 버튼 나타나게 하는 부분
//        if(facebookButton.style.display=="none") {
//          facebookButton.style.display = "block";
//        }
//      }
//    }
//
//    // 로그인 상태 체크하기
//    function checkLoginState() {
//      FB.getLoginStatus(function(response) {
//        statusChangeCallback(response);
//      });
//    }
//
//    window.fbAsyncInit = function() {
//      FB.init({
//        appId      : '1728710870700206', // ramo appID
//        cookie     : true,  // enable cookies to allow the server to access 
//                            // the session
//        xfbml      : true,  // parse social plugins on this page
//        version    : 'v2.2' // use version 2.2
//      });
//
//      FB.getLoginStatus(function(response) {
//        statusChangeCallback(response);
//      });
//    };
//
//    // 비동기적으로 SDK 호출하는 부분
//    (function(d, s, id) {
//      var js, fjs = d.getElementsByTagName(s)[0];
//      if (d.getElementById(id)) return;
//      js = d.createElement(s); js.id = id;
//      js.src = "//connect.facebook.net/en_US/sdk.js";
//      fjs.parentNode.insertBefore(js, fjs);
//    }(document, 'script', 'facebook-jssdk'));
//
//    // 페이스북 로그인이 정상적으로 되면 이 부분을 호출한다.
//    function getUserInfo() {
//      // 여기에 Graph API 부분의 Query를 넣어준다
//      FB.api('/me?fields=name,gender,bio,birthday,education,hometown,sports,inspirational_people,music{name},political,interested_in,work,religion,relationship_status', function(response) {
//        $.ajax({
//          type: "post",
//          url: "getMusicFB.php",
//          data: JSON.stringify(response), // 페이스북 정보
//          dataType: "json",
//          success: function(result) {
//            console.log("Success");
//
//            myPlayerObject.setPlaylist(result, false); // 자동재생하지 않고
//            sleep(1000); // 1초 sleep 했다가
//            myPlayerObject.play(); // 재생목록 재생
//          },
//          error: function(err) {
//            console.log(err);
//          }
//        });
//      });
//    }
    
    // Facebook 연동 관련된 부분 ----------------------------------------------------------------------
    
    
    // 플레이어 나타나게 하는 부분
    function callMediaPlayer(result) {
      console.log("consoleMediaPlayer : " + result);
      
      // Wimpy Player가 준비되었을때 호출되는 부분
      wimpy.onReady(function(){
        var myPlayer = document.getElementById('myPlayer'); // Player 객체 받는 부분
        
        // Player 나타나게 하는 부분
        if(myPlayer.style.display=="none") {
          myPlayer.style.display = "block";
        }
        
        // 객체 받는 부분
        myPlayerObject = wimpy.getPlayer("myPlayer");
        playerInfo = myPlayerObject.getStatus();
        
        // 세션으로부터 로그인 정보 받는 부분
        var username = '<?php echo $_SESSION['username'] ?>';
        
        // 알고리즘에 로그인 했다고 보내고 음악을 받는 부분
//        getMusic(username, 'login');
        
        var playButton = myPlayerObject.getSkinElement("cmp_play"); // play button
        var prevButton = myPlayerObject.getSkinElement("cmp_rewind"); // previous button
	    var nextButton = myPlayerObject.getSkinElement("cmp_next"); // next button
        
        // 이벤트 리스너 등록하는 부분
        myPlayerObject.addListener("play", this.playMusic, this);
        myPlayerObject.addListener("done", this.doneMusic, this);
        
        playButton.addMEL("mouseDown", playBtn, this, "Argument for play button.");
        prevButton.addMEL("mouseDown", prevBtn, this, "Argument for previous button.");
        nextButton.addMEL("mouseDown", nextBtn, this, "Argument for next button.");
        
        setInterval(pollPlayer, 1000);
        
        // 누군가 추천 음악이 있는지 없는지 확인하는 부분
        getRecommend();
        
      });
    }
    
    /**
    * Delay for a number of milliseconds
    */
    function sleep(delay) {
      var start = new Date().getTime();
      while (new Date().getTime() < start + delay);
    }
    
    // 재생길이 및 현재 재생시간을 위한 폴링 함수
    function pollPlayer(){
      console.log('pollPlayer()');
      var info = myPlayerObject.getStatus();
      
      current_nice = info.current_nice;
      duration_nice = info.duration_nice;
      
      console.log('current_nice : ' + current_nice);
      
      if(current_nice == '1:00') {
        afterOneMinute();
      }
      
    }
    
    // 1 분이 지나면 이벤트 넣어주는 부분
    function afterOneMinute() {
      console.log('afterOneMinute()');
      console.log('current_nice : ' + current_nice);
      console.log('duration_nice : ' + duration_nice);
      
      var username = '<?php echo $_SESSION['username'] ?>';
      var info = myPlayerObject.getTrackDataset();
      
      var title; // 노래 제목
      var file; // 파일 경로

      for(var prop in info){
        if(prop == 'title') {
          title = info[prop];
        }

        if(prop == 'file') {
          file = info[file];
        }
      }
      
      getMusic(username, 'keep');
      insertEvent(username, 'keep', title, current_nice, duration_nice);
    }
    
    // eos : 음악이 끝난경우 호출되는 함수    
    function doneMusic() {
      console.log('doneMusic()');
      console.log('current_nice : ' + current_nice);
      console.log('duration_nice : ' + duration_nice);
      
      var username = '<?php echo $_SESSION['username'] ?>';
      var info = myPlayerObject.getTrackDataset();
      
      var title; // 노래 제목
      var file; // 파일 경로

      for(var prop in info){
        if(prop == 'title') {
          title = info[prop];
        }

        if(prop == 'file') {
          file = info[file];
        }
      }
      
      insertEvent(username, 'done', title, current_nice, duration_nice);
      getMusic(username, 'eos');
    }
    
    // 음악이 재생될때 호출되는 부분
    function playMusic() {
      console.log('playMusic()');
      console.log('current_nice : ' + current_nice);
      console.log('duration_nice : ' + duration_nice);
      
      var playlist = myPlayerObject.getPlaylist();
      
      if(skipCount == null) {
        skipCount = 0;
      }
      
      console.log('skipCount : ' + skipCount);
      current_music = playlist[skipCount].file;
      
      // 추천음악을 재생하면 곡이 하나기 때문에 미리 플레이 리스트를 받아놔야함
      if(recommend_music_playing == true) {
        console.log('playMusic() recommend_music_playing is true');
        
        var username = '<?php echo $_SESSION['username'] ?>';
        myPlayerObject.clearPlaylist();
        console.log("clearPlaylist()!!!!!!!");
        
        getMusic(username, 'eos');
        recommend_music_playing = false; // 이 이후로는 추천음악이 아니기 때문에 false로 초기화
      }
     
      if (skipCount == 2) {
        var username = '<?php echo $_SESSION['username'] ?>';
        myPlayerObject.clearPlaylist();
        console.log("clearPlaylist()!!!!!!!");
        
        // 어차피 skip을 하든 eos하든 결과적으로 eos를 넘겨서 리스트를 받기때문에 미리 리스트를 새로 만들어 놓음
        getMusic(username, 'eos');
      }
      else if (skipCount >= 3) {
        skipCount = 0;
      }
      
      var username = '<?php echo $_SESSION['username'] ?>';
      var info = myPlayerObject.getTrackDataset();
      
      var title; // 노래 제목
      var file; // 파일 경로

      for(var prop in info){
        if(prop == 'title') {
          title = info[prop];
        }

        if(prop == 'file') {
          file = info[file];
        }
      }
      
      insertEvent(username, 'playMusic', title, current_nice, duration_nice);
    }
    
    // 재생버튼 눌렀을때 호출되는 부분
    function playBtn() {
      var username = '<?php echo $_SESSION['username'] ?>';
      var info = myPlayerObject.getTrackDataset();
      
      var title; // 노래 제목
      var file; // 파일 경로

      for(var prop in info){
        if(prop == 'title') {
          title = info[prop];
        }

        if(prop == 'file') {
          file = info[file];
        }
      }
      
      // playpause == 0 : stop
      // playpause == 1 : play
      if(myPlayer.playpause == 0) {
        console.log('playBtn()');
        console.log('current_nice : ' + current_nice);
        console.log('duration_nice : ' + duration_nice);
        
        insertEvent(username, 'playBtn', title, current_nice, duration_nice);
      }
      else {
        console.log('pauseBtn()');
        console.log('current_nice : ' + current_nice);
        console.log('duration_nice : ' + duration_nice);
        
        insertEvent(username, 'pauseBtn', title, current_nice, duration_nice);
      }
    }
    
    // 이전버튼 눌렀을때 호출되는 부분
    function prevBtn(obj, pos, arg){
      console.log('prevPlayer()');
      console.log("prevButtonClicked", obj, pos, arg);
      
      var username = '<?php echo $_SESSION['username'] ?>';
      var info = myPlayerObject.getTrackDataset();
      
      var title; // 노래 제목
      var file; // 파일 경로

      for(var prop in info){
        if(prop == 'title') {
          title = info[prop];
        }

        if(prop == 'file') {
          file = info[file];
        }
      }
      
      insertEvent(username, 'prevBtn', title, current_nice, duration_nice);
    }
    
    // skip버튼 눌렀을대 호출되는 부분
    function nextBtn(obj, pos, arg){
      console.log('nextPlayer()');
      console.log("nextButtonClicked", obj, pos, arg);
      
      var username = '<?php echo $_SESSION['username'] ?>';
      var info = myPlayerObject.getTrackDataset();
      
      skipCount++;
      
      console.log("skipCount : " + skipCount); // for Test
      
      getMusic(username, 'skip');
      
      var title; // 노래 제목
      var file; // 파일 경로

      for(var prop in info){
        if(prop == 'title') {
          title = info[prop];
        }

        if(prop == 'file') {
          file = info[file];
        }
      }
      
      insertEvent(username, 'nextBtn', title, current_nice, duration_nice);
    }
    
    // 이벤트를 받아서 새로운 음악리스트(=3개의 음악)를 받는 부분
    function getMusic(username, event) {
      if (event == 'skip') {
        console.log("getMusic username : " + username + " event : skip");
        // skip 처리때
        $.ajax({
          type: "post",
          url: "algorithm/getMusic.php",
          data: {
            username: username,
            event_action: "skip"
          },
          success: function(result) {
            console.log("skip Success");
          },
          error: function(err) {
            console.log(err);
          }
        });
      }
      else if (event == 'keep') {
        console.log("getMusic username : " + username + " event : keep");
        // skip 처리때
        $.ajax({
          type: "post",
          url: "algorithm/getMusic.php",
          data: {
            username: username,
            event_action: "keep"
          },
          success: function(result) {
            console.log("keep Success");
          },
          error: function(err) {
            console.log(err);
          }
        });
      }
      // login 이벤트 처리때
      else if (event == 'login') {
        console.log("getMusic username : " + username + " event : login");
        $.ajax({
          type: "post",
          url: "algorithm/getMusic.php",
          data: {
            username: username,
            event_action: event
          },
          dataType: "json",
          async: false, // 동기화 처리
          timeout: 50000,
          success: function(result) {
            console.log("getMusic Success : " + result);
            
            myPlayerObject.setPlaylist(result, false); // 자동재생하지 않고
            
            var playlist = myPlayerObject.getPlaylist();
            console.log("new playlist[0].title : " + playlist[0].title);
            console.log("new playlist[1].title : " + playlist[1].title);
            console.log("new playlist[2].title : " + playlist[2].title);
            
            if(event == 'login') {
              sleep(500);
              myPlayerObject.setInfo("Press Play"); // 디스플레이에 나오는 정보
            }
            
          },
          error: function(err) {
            console.log(err);
          }
        });
      }
      // eos 이벤트 처리때
      else {
        console.log("getMusic username : " + username + " event : eos");
        $.ajax({
          type: "post",
          url: "algorithm/getMusic.php",
          data: {
            username: username,
            event_action: event
          },
          dataType: "json",
          timeout: 50000,
          success: function(result) {
            console.log("getMusic Success : " + result);
            
            myPlayerObject.setPlaylist(result, false); // 자동재생하지 않고
            
            var playlist = myPlayerObject.getPlaylist();
            console.log("new playlist[0].title : " + playlist[0].title);
            console.log("new playlist[1].title : " + playlist[1].title);
            console.log("new playlist[2].title : " + playlist[2].title);
            
            if(event == 'login') {
              sleep(500);
              myPlayerObject.setInfo("Press Play"); // 디스플레이에 나오는 정보
            }
            
          },
          error: function(err) {
            console.log(err);
          }
        });
      }
    }
    
    // Database에 이벤트 넣는 함수
    function insertEvent(username, event_action, music_title, current_nice, duration_nice) {
      if(current_nice == null) {
        current_nice = '0:00';
      }
      
      if(duration_nice == null) {
        duration_nice = '0:00';
      }
      
      console.log('insertEvent()');
      console.log('username : ' + username);
      console.log('event_action : ' + event_action);
      console.log('music_title : ' + music_title);
      console.log('current_nice : ' + current_nice);
      console.log('duration_nice : ' + duration_nice);
      
      $.ajax({
        type: "post",
        url: "insertEvent.php",
        timeout: 50000,
        data: {
          username: username,
          event_action: event_action,
          music_title: music_title,
          current_nice: current_nice,
          duration_nice: duration_nice
        },
        dataType: 'text',
        success: function(html) {
          console.log('insert event success ' + html);
        },
        error: function (textStatus, errorThrown) {
            console.log('insert evnet err');
            console.log(textStatus);
            console.log(errorThrown);
        }
      });
    }

  </script>
   
  <!-- Bootstrap core JavaScript -->
  <!-- Placed at the end of the document so the pages load faster -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

  <!-- Latest compiled and minified JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    
  </body>
</html>