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

      public static function logout(){
        DB::query("DELETE FROM webchat_users WHERE name = '".DB::esc($_SESSION['user']['name'])."'");
        
      }
    }
  }
?>
