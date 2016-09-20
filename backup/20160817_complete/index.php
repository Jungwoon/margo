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
    <div class="form-signin">
     <!-- Wimpy Player -->
     <div id="myPlayer" data-skin="/wimpy/wimpy.skins/margo_no_playlist.tsv" style="display:block" data-wimpyplayer></div>
     
      <!-- scope에는 필요한 권한 범위를 설정해줘야 한다. -->
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

    <a href="login/logout.php" class="btn btn-default btn-lg btn-block">Sign out</a>
    </div> <!-- /form-signin -->
  </div> <!-- /container --> 
   
  <script>
    
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
//          url: "cbMusic.php",
//          data: JSON.stringify(response),
//          dataType: "text",
//          success: function(result) {
//            console.log("Success");
//
//            // 결과로 받은 음악 리스트를 Wimpy로 넘김
//            callMediaPlayer(result);
//          },
//          error: function(err) {
//            console.log(err);
//          }
//        });
//      });
//    }
    
    // Facebook 연동 관련된 부분 ----------------------------------------------------------------------
    
    // 전역변수로 선언
    var myPlayerObject;
    var playerInfo;
    var duration_nice; // 음악 전체시간
    var current_nice; // 현재 재생시간
    var skipCount = 0;
    
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
        getMusic(username, 'login');
        
        var playButton = myPlayerObject.getSkinElement("cmp_play"); // play button
        var prevButton = myPlayerObject.getSkinElement("cmp_rewind"); // previous button
	    var nextButton = myPlayerObject.getSkinElement("cmp_next"); // next button
        
        // 이벤트 리스너 등록하는 부분
        myPlayerObject.addListener("play", this.playMusic, this);
        myPlayerObject.addListener("done", this.doneMusic, this);
        
        playButton.addMEL("mouseDown", playBtn, this, "Argument for play button.");
        prevButton.addMEL("mouseDown", prevBtn, this, "Argument for previous button.");
        nextButton.addMEL("mouseDown", nextBtn, this, "Argument for next button.");
        
        setInterval(pollPlayer, 500);
        
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
      
      if(skipCount >= 3) {
        getMusic(username, 'eos');
        skipCount = 0; // 초기화
      }
      else {
        skipEventForAlgorithm(username);
      }
      
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
      console.log("getMusic username : " + username + " event : " + event);
      
      $.ajax({
          type: "post",
          url: "algorithm/getMusic.php",
          data: {
            username: username,
            event_action: event,
          },
          dataType: "json",
          async: false, // 동기화 처리
          timeout: 30000,
          success: function(result) {
            console.log("getMusic Success : " + result);
            
            myPlayerObject.setPlaylist(result, false); // 자동재생하지 않고
            sleep(1000); // 1초 sleep 했다가
            myPlayerObject.play(); // 재생목록 재생
          },
          error: function(err) {
            console.log(err);
          }
        });
    }
    
    // Skip버튼을 눌렀을때 알고리즘에 skip을 했다고 넘겨주는 부분
    function skipEventForAlgorithm(username) {
      console.log("skipEventForAlgorithm username : " + username);
      
      $.ajax({
          type: "post",
          url: "algorithm/getMusic.php",
          data: {
            username: username,
            event_action: "skip",
          },
          success: function(result) {
            console.log("skip Success");
          },
          error: function(err) {
            console.log(err);
          }
        });
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
        timeout: 30000,
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
    
  </body>
</html>