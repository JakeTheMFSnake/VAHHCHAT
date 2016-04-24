<?php
/*This file handles the AJAX requests sent from the
jQuery front end and outputs JSON formatted data.*/
/* JSON == JavaScript Object Notaion*/

/*Getting files needed/ require: */

/*End of required files*/

/*Create and Start session webchat*/
sesson_name('webchat');
session_start();

/*Gets the current configuration setting of magic_quotes_gpc*/
/*Returns 0 if magic_quotes_gpc is off, 1 otherwise. Or always
returns FALSE as of PHP 5.4.0.*/
if(get_magic_quotes_gpc()){

    // If magic quotes is enabled, strip the extra slashes
    array_walk_recursive($_GET,create_function('&$v,$k','$v = stripslashes($v);'));
    array_walk_recursive($_POST,create_function('&$v,$k','$v = stripslashes($v);'));
}

try {
  // Connecting to the database
  DB::init($dbOptions);

  $response = array();

  // Handling the supported actions:
  switch($_GET['action']){
    case 'login':
      
    break;

    case 'checkLogged':

    break;

    case 'logout':

    break;

    case 'submitChat':

    break;

    case 'getUser':

    break;

    case 'getChats':

    break;

    default:
      throw new Exception('Wrong action');
  }
}

?>
