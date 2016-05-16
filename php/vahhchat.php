<?php
/*This file handles the AJAX requests sent from the
jQuery front end and outputs JSON formatted data.*/
/* JSON == JavaScript Object Notaion*/

/*Variables needed to connect to the database */
$dbOptions = array(
	'db_host' => 'jakobhakansson.se.mysql',
  'db_user' => 'jakobhakansson_se',
	'db_pass' => 'owSZyxEK',
	'db_name' => 'jakobhakansson_se'
);

error_reporting(E_ALL ^ E_NOTICE);

/*Getting files needed/ require: */
require "classes/DB.class.php";
require "classes/Chat.class.php";
require "classes/ChatBase.class.php";
require "classes/ChatLine.class.php";
require "classes/ChatUser.class.php";

/*End of required files*/

/*Create and Start session webchat*/

session_name('webchat');
session_start();

/*Gets the current configuration setting of magic_quotes_gpc*/
/*Returns 0 if magic_quotes_gpc is off, 1 otherwise. Or always
returns FALSE as of PHP 5.4.0.*/
if(get_magic_quotes_gpc()){

	// If magic quotes is enabled, strip the extra slashes
	array_walk_recursive($_GET,create_function('&$v,$k','$v = stripslashes($v);'));
	array_walk_recursive($_POST,create_function('&$v,$k','$v = stripslashes($v);'));
}

try{

	// Connecting to the database
	DB::init($dbOptions);

	$response = array();

	/* Handling the supported actions:
  All outputs in the form of JSON messages,
  and errors in the form of exceptions.
  All requests will be routed to appropriate static
  methods of the Chat class*/
	switch($_GET['action']){

		case 'login':
			$response = Chat::login($_POST['name'],$_POST['email']);
		break;
		//Calls for the static method checkLogged in the Chat class.
		case 'checkLogged':
			$response = Chat::checkLogged();
		break;
		//Calls for the static method logout in the Chat class.
		case 'logout':
			$response = Chat::logout();
		break;
		//Posts the chatText for the static method submitChat in the Chat class.
		case 'submitChat':
			$response = Chat::submitChat($_POST['chatText']);
		break;
		//Calls for the static method getUser in the Chat class.
		case 'getUsers':
			$response = Chat::getUsers();
		break;
		//Gets the latest chatMessages from the static method getChats in the Chat class.
		case 'getChats':
			$response = Chat::getChats($_GET['lastID']);
		break;
		//Creates a new exception "Wrong action".
		default:
			throw new Exception('Wrong action');
	}
	/*Translates the data passed to it to a JSON string which can
  then be output to a JavaScript variable*/
	echo json_encode($response);
}
catch(Exception $e){
	die(json_encode(array('error' => $e->getMessage())));
}

?>
