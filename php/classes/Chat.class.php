<?php

/* The Chat class exploses public static methods, used by ajax.php */

class Chat{
	/*Loggs in the user with it's name*/
	public static function login($name){
		if(!$name){
			throw new Exception('Fill in all the required fields plox! <3');
		}




		$user = new ChatUser(array(
			'name'		=> $name
		));

		// The save method returns a MySQLi object
		if($user->save()->affected_rows != 1){
			throw new Exception('This nick is in use.');
		}
		//Loggs the session.
		$_SESSION['user']	= array(
			'name'		=> $name
		);
		// If we've made it this far, everything checks out.
		return array(
			'status'	=> 1,
			'name'		=> $name
		);
	}
	/*Checkes if the user with name is logged*/
	public static function checkLogged(){
		$response = array('logged' => false);

		if($_SESSION['user']['name']){
			$response['logged'] = true;
			$response['loggedAs'] = array(
				'name'		=> $_SESSION['user']['name']
			);
		}

		return $response;
	}
	/*Removes the user from the chat and database*/
	public static function logout(){
		DB::query("DELETE FROM webchat_users WHERE name = '".DB::esc($_SESSION['user']['name'])."'");
		// frees all session variables currently registered.
		$_SESSION = array();
		unset($_SESSION);

		return array('status' => 1);
	}
	/*Submits a chat message*/
	public static function submitChat($chatText){
		//Checks if the user is logged in.
		if(!$_SESSION['user']){
			throw new Exception('You are not logged in');
		}
		//Checks so there is a message to send.
		if(!$chatText){
			throw new Exception('You haven\'t entered a chat message.');
		}
		//Creates the chat to be sent with appropriate information.
		$chat = new ChatLine(array(
			'author'	=> $_SESSION['user']['name'],

			'text'		=> $chatText
		));

		// The save method returns a MySQLi object
		$insertID = $chat->save()->insert_id;

		return array(
			'status'	=> 1,
			'insertID'	=> $insertID
		);
	}
	/*A function that will be used every 15 sec, that deletes
  messages older than 5 min and inactive useres from the database. */
	public static function getUsers(){
		if($_SESSION['user']['name']){
			$user = new ChatUser(array('name' => $_SESSION['user']['name']));
			$user->update();
		}

		/*Deleting chat messages that is older than 5 min
    and users that haven't been active the last 30 sec.*/
		DB::query("DELETE FROM webchat_lines WHERE ts < SUBTIME(NOW(),'0:5:0')");
		DB::query("DELETE FROM webchat_users WHERE last_activity < SUBTIME(NOW(),'0:0:30')");

		$result = DB::query('SELECT * FROM webchat_users ORDER BY name ASC LIMIT 18');

		$users = array();
		//Here we might get some problems ====
		while($user = $result->fetch_object()){

			$users[] = $user;
		}

		return array(
			'users' => $users,
			'total' => DB::query('SELECT COUNT(*) as cnt FROM webchat_users')->fetch_object()->cnt
		);
	}
	/*Gets the latest chat messages within the interval*/
	public static function getChats($lastID){
		$lastID = (int)$lastID;

		$result = DB::query('SELECT * FROM webchat_lines WHERE id > '.$lastID.' ORDER BY id ASC');

		$chats = array();
		while($chat = $result->fetch_object()){
			/*gmdate function to output a GMT time. In the frontend,
      we use the hour and minute values to feed the JavaScript
      date object, and as a result all the times are displayed
      in the user’s local time.*/
			$chat->time = array(
				'hours'		=> gmdate('H',strtotime($chat->ts)),
				'minutes'	=> gmdate('i',strtotime($chat->ts))
			);



			$chats[] = $chat;
		}

		return array('chats' => $chats);
	}

}


?>
