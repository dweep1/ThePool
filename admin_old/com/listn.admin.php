<?php 

include "./admin.header.php";

global $pdo;

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    
    if(!isset($_POST['submitType'])){
        fail('Submit Type Not Set');
    }
    
    $submitType = intval($_POST['submitType']);
    
    if($submitType == 0){//generate key
        
        $email = clean($_POST['email']);
        
        fail(user::generateKey($email));
        
    }else if($submitType == 1){//send key to email
        
        $email = clean($_POST['email']);
        
        $key = user::generateKey($email);
        
        $keyformat = @$_SERVER[HTTP_HOST]."/register.php?key=".$key;
        
        $subject = 'NFL - The Pool invite, attempt number 2!';
         
        $message = "<a href=\"$keyformat\">Click Here</a> to register.<br/>
        <br/>
        <br/>
        If the link above does not work, then try and copy the following address into your web browser.<br/>
        (Or if the following is a link, try clicking it instead, or both!)<br/>
        <br/>
        $keyformat<br/>
        <br/>
        <br/>
        *If the above is not copy pasted in whole, then part of your key may be cut off. please ensure that the key remains intact.<br/>
        <br/>
        This is an automated response, please do not reply!<br/>";
         
        $headers = "From: no-reply<noreply@whats-your-confidence.com>\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        
        if(@mail($email, $subject, $message, $headers)){
          //fail('Success! Please Check Your Email');//sent the email, back to login page
        }else{
          fail('Failed to Send Email');//failed to send email, email server down
        }
        
        fail($keyformat);
        
    }else if($submitType == 2){//resend key
        
        $id = intval($_POST['link_id']);
        
        $email = user::getKeyEmail($id);
        
        $key = user::getKey($id);
        
        $keyformat = @$_SERVER[HTTP_HOST]."/register.php?key=".$key;
        
        $subject = 'You have been invited to join The Pool!';
         
        $message = "<a href=\"$keyformat\">Click Here</a> to register.<br/>
        <br/>
        <br/>
        If the link above does not work, then try and copy the following address into your web browser.<br/>
        <br/>
        $keyformat<br/>
        <br/>
        <br/>
        *If the above is not copy pasted in whole, then part of your key may be cut off. please ensure that the key remains intact.<br/>
        <br/>
        This is an automated response, please do not reply!<br/>";

        $headers = "From: no-reply<noreply@whats-your-confidence.com>\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        
        if(@mail($email, $subject, $message, $headers)){
          //fail('Success! Please Check Your Email');//sent the email, back to login page
        }else{
          fail('Failed to Send Email');//failed to send email, email server down
        }
        
        fail($keyformat);
        
    }else if($submitType == 3){ //load user info to edit
        
        $id = intval($_POST['user_id']);
        
        fail(json_encode(user::getUserData($id)));
        
    }else if($submitType == 4){ //submit changed user data
        
        if(user::updateUserData($_POST)){
            fail("Successfully Updated User");
        }
        
        fail("Couldn't Update User");
        
    }else if($submitType == 5){ //load user info to edit
        
        $id = intval($_POST['user_id']);
        
        fail(json_encode(user::getUserData($id)));
        
    }else if($submitType == 6){ //create new user from admin
        
        if(!isset($_POST['username']) || $_POST['username'] == "Username"){
            fail('please enter your username');
        }
         
        if(!isset($_POST['password']) || $_POST['password'] == "Password" || $_POST['password'] === "0"){
            fail('please enter a password');
        }
         
        if(!isset($_POST['email']) || $_POST['email'] == "Email"){
            fail('please enter your username');
        }
 
        $user = $_POST;
        
        //get username and password
        $username = clean($user['username']);
        $email = clean($user['email']);
        $pass = clean($user['password']);
        $favorite_team = "".intval($user['favorite_team']);
        $user_level = intval($user['user_level']);
        $access_level = intval($user['access_level']);
        
        //generate a salt with microtime and password
        $salt = s::hash(microtime() . $pass);
        $secure = s::hash(microtime().$salt);
        
        // Generate password from salt
        $password = s::add_salt($pass, $salt);
        
        $userdata = array('username' => $username, 'email' => $email, 'password' => $password, 'salt' => $salt, 'user_level' => $user_level, 'security_key' => $secure, 'favorite_team_id' => $favorite_team, 'access_level' => $access_level);
        
        if(!@user::write_new_user($userdata)){
         fail('Username Already Exists');
        }
        
        $subject = 'An Account at The Pool has been registered Using this Email!';
        
        $message = "You've been regestered for an account at NFL - The Pool<br /><br />
        
        Click this link and login.<br /><br />
        
        <a href='$_SERVER[HTTP_HOST]'>$_SERVER[HTTP_HOST]</a><br /><br />
        
        Thanks!<br />
        Site admin<br /><br />
        
        This is an automated response, please do not reply!";
        
        $headers = "From: no-reply<noreply@whats-your-confidence.com>\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        
        if(@mail($email, $subject, $message, $headers)){
            fail('Success! Please Check Your Email');//sent the email, back to login page
        }else{
            fail('Failed to Send Email');//failed to send email, email server down
        }
         
        
    }else if($submitType == 7){ //delete user
        $id = $_POST['user_id'];
        
        if(user::deleteUser($id)){

			if(pool::deletePick(-1, $id)){
            	fail("Successfully Deleted User");
			}

			fail("Couldn't Delete User Picks");
        }
        
        fail("Couldn't Delete User");
    
    }else if($submitType == 8){ //load season data
    
        $id = intval($_POST['season_id']);
        
        fail(json_encode(pool::getSeasonData($id)));
    
    }else if($submitType == 9){ //update season data
    
        if(pool::updateSeasonData($_POST)){
        
            fail("Successfully Updated Season Data");
            
        }
        
        fail("Failed to Update Season Data");
    
    }else if($submitType == 10){ //create new season
        
        $_POST['date_start'] = DB::unixToMySQL($_POST['date_start']);
    
        if(pool::createSeason($_POST)){
        
            fail("Successfully Updated Season Data");
            
        }
        
        fail("Failed to Update Season Data");
    
    }else if($submitType == 11){ //lock/unlock season
    
        $id = intval($_POST['season_id']);
        
        fail(json_encode(pool::getSeasonData($id)));
    
    }else if($submitType == 12){ //get/set current season cookie
        
        fail(pool::getCurrentSeason());
    
    }else if($submitType == 13){ //load game data
        
        $id = intval($_POST['game_id']);
        
        fail(json_encode(pool::getGameData($id)));
    
    }else if($submitType == 14){ //update game data
        
        $response = pool::updateGameData($_POST);
    
        fail(var_dump($response));
        
        //fail("Failed to Update Game Data");
    
    }else if($submitType == 15){ //lock season or set current season
        
        $id = intval($_POST['season_id']);
        
        if(intval(pool::getCurrentSeason()) === $id){
            
            if(pool::toggleSeasonLock()){
            
                fail("Successfully Toggled Season Lock");
            
            }
            
        }else{
            
            if(pool::setCurrentSeason($id)){
            
                fail("Successfully Set Current Season");
            
            }
            
        }

        fail("Failed to Update Game Data");
    
    }else if($submitType == 16){ //set game to a bye game
    
        $id = intval($_POST['game_id']);
        
        if(pool::setByeGame($id)){
        
            fail("Successfully Updated Game Data");
            
        }
        
        fail("Failed to Update Game Data");
    
    }else if($submitType == 17){ //update pick GET pick data

		$user_id = pool::getSelectedUserID();
		$game_id = $_POST['game_id'];

		fail(json_encode(pool::getPicked($game_id, $user_id)));

	}else if($submitType == 18){ //update pick GET game data

		$game_id = $_POST['game_id'];

		fail(json_encode(pool::getGameDataFull($game_id)));

	}else if($submitType == 19){ //update pick POST data

		$team_id = $_POST['team_id'];
		$game_id = $_POST['game_id'];
		$user_id = pool::getSelectedUserID();
		$value = $_POST['value'];

		$pick = pool::getPicked($game_id, $user_id);

		$done = "".pool::setPickCurrentWeek($team_id, $game_id, $user_id, $value);
		$done .= pool::updatePickResult($game_id);

		$new_pick = pool::getPicked($game_id, $user_id);

		$updates = 0;

		if(isset($pick['id'])){

			if(intval($new_pick['team_id']) !== intval($pick['team_id'])){
				$updates++;
			}

			if(intval($new_pick['value']) !== intval($pick['value'])){
				$updates++;
			}

		}

		if(pool::hasGameBeenPlayed($game_id)){

			if($updates > 0){

				$done .= userstat::correctChange($pick, $new_pick);
			}
		}

		return $done;

	}else if($submitType == 20){ //refresh season stats.

		$result = userstat::refreshSeasonStats();

		fail($result === true ? "Success" : $result);

	}else if($submitType == 21){ //refresh season stats.

		$date = new DateTime("now", pool::getTimestamp());

		$message = $_POST['message']."<br/><br/><br/>This is a reminder to get your picks in this week.<br/><br/>";
		$subject = "An NFL Confidence Pick Reminder!";

		if($_POST['group']  == 0){
			$date->modify('-3 week');
		}else if($_POST['group']  == 1){
			$date->modify('-17 week');
		}else{
			$date->modify('-3 week');
		}

		$current_week = pool::getCurrentWeek();

		$current_week_games = pool::getGamesByWeek($current_week['id']);

		$date = $date->format("Y-m-d");

		$query = $pdo->prepare("SELECT username, user_id, email FROM pick INNER JOIN users ON pick.user_id = users.id WHERE `date` >= :today GROUP BY user_id ORDER BY user_id ASC");

		$query->execute(array(':today' => $date));

		$results = $query->fetchAll(PDO::FETCH_ASSOC);

		$headers = "From: no-reply<noreply@whats-your-confidence.com>\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		$count = 0;

		foreach($results as $value){

			if($_POST['group']  == 0){

				$pickCount = userstat::getWeekPickCount($value['user_id'], $current_week['id']);

				if($pickCount < count($current_week_games)){
					$message .= "{$value['username']}'s Current Number of Picks Submitted: $pickCount";
					@mail($value['email'], $subject, $message, $headers);
					$count++;
				}
			}else if($_POST['group']  == 1){
				@mail($value['email'], $subject, $message, $headers);
				$count++;
			}else if($_POST['group']  == -1){//testing

				$pickCount = userstat::getWeekPickCount($value['user_id'], $current_week['id']);

				if(strpos($value['email'],'matkle414') !== false){
					$message .= "{$value['username']}'s Current Number of Picks Submitted: $pickCount";
					@mail($value['email'], $subject, $message, $headers);
					$count++;
					fail(" Emailed ".$count." Recipients");
				}
			}




		}

		fail(" Emailed ".$count." Recipients");

	}



    
    
    
}

?>