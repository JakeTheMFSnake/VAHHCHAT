<?php
  class chat {

    public static function login($name, $email){
      if(!$name || $email){
        throw new Exception('Fill in all the required fields plox! <3');
      }
      if(!filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)){
        throw new Exception('Dude, that aint be no email yo!');
      }
      // Preparing the gravatar hash:
      $gravatar = md5(strtolower(trim($email)));
      $user = new ChatUser(array(
        'name'  => $name,
        'gravatar'  => $gravatar
      ));

      // The save method will return a MySQLi object
      if($user->save()->affected_rows != 1){
        throw new Exception('The choosen swag-tag is already in use!');
      }

      $_SESSION['user'] = array(
        'name'  => $name,
        'gravatar'  =>$gravatar
      );

      return array(
        'status'  => 1,
        'name'  => $name,
        'gravatar'  => Chat::gravatarFromHash($gravatar)
      );

      /**/
      public static function checkLogged(){
        $response = array('logged' => false);

        if($_SESSION['user']['name']){
          $response['logged'] = true;
          $response['loggedAs'] = array(
            'name'  => $_SESSION['user']['name'],
            'gravatar' => Chat::gravatarFromHash($_SESSION['user']['gravatar'])
          );
        }

        return $response;
      }

      /*Removes the user from the chat and database*/
      public static function logout(){
        DB::query("DELETE FROM webchat_users WHERE name = '".DB::esc($_SESSION['user']['name'])."'");
        $_SESSION = array();
        // frees all session variables currently registered.
        unset($_SESSION);

        return array('status' => 1);
      }

      /*Submits a chat message*/
      public static function submitChat($chatText){
        if(!$_SESSION['user']){
          throw new Exception('You are not logged in my friend');
        }
        if(!$chatText){
          throw new Exception('No message was entered my friend');
        }
        $chat = new ChatLine(array(
          'author' => $_SESSION['user']['name'],
          'gravatar' => $_SESSION['user']['gravatar'],
          'text'  => $chatText
        ));
        // This metod will return a MySQLi object
        $insertID = $chat->save()->insert_id;
        return array(
          'status' => 1,
          'insertID'  => $insertID
        );
      }

      /*A function that will be used every 15 sec, that deletes
      messages older than 5 min and inactive useres from the database. */
      public static function getUser(){
        if($_SESSION['user']['name']){
          $user = new ChatUser(array('name' => $_SESSION['user']['name']));
          $user->update();
        }

        /*Deleting chat messages that is older than 5 min
        and users that haven't been active the last 30 sec.*/
        DB::query("DELETE FROM webchat_lines WHERE ts < SUBTIME(NOW(), '0:5:0')");
        DB::query("DELETE FROM webchat_users WHERE last_activity < SUBTIME(NOW(), '0:0:30')");

        $result = DB::query('SELECT * FROM webchat_users ORDER BY name ASC LIMIT 18');

        $user = array();
        while($user = $result->fetch_object()){
          $user->gravatar = Chat::gravatarFromHash($user->gravatar,30);
          $users[] = $user;
        }

        return array(
          'users' => $users,
          'total' => DB::query('SELECT COUNT(*) as cnt FROM webchat_users')->fetch_object->cnt
        );
      }

      /**/
      public static function getChats($lastID){
        $lastID = (int)$lastID;

        $result = DB:query('SELECT * FROM webchat_lines WHERE id > '.$lastID.' ORDER BY id ASC');

        $chats = array();
        while($chat = $result->fetch_object()){
          /*gmdate function to output a GMT time. In the frontend,
          we use the hour and minute values to feed the JavaScript
          date object, and as a result all the times are displayed
          in the user’s local time.*/
          $chat->time = array(
            'hours' => gmdate('H',strtotime($chat->ts)),
            'minutes' => gmdate('i',strtotime($chat->ts))
          );
          $chat->gravatar = Chat::gravatarFromHash($chat->gravatar);
          $chats[] = $chat;
        }
        return array('chats' => $chats);
      }

      /*Plugin-in from gravatar.com to be able to use your gravatar*/
      public static function gravatarFromHash($hash, $size=23){
        return 'http://www.gravatar.com/avatar/'.$hash.'?size='.$size.'&default='.
                urlencode('http://www.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?size='.$size);
      }


    }
  }
?>
