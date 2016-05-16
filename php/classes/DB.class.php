<?php

class DB {
	private static $instance;
	private $MySQLi;

	/*Creates a connection to the database*/
	private function __construct(array $dbOptions){

		$this->MySQLi = @ new mysqli(	$dbOptions['db_host'],
										$dbOptions['db_user'],
										$dbOptions['db_pass'],
										$dbOptions['db_name'] );

		if (mysqli_connect_errno()) {
			throw new Exception('Database error.');
		}

		$this->MySQLi->set_charset("utf8");
	}
	/*Takes an array with MySQL login details, and creates an instance of the class,
	held in the self::$instance static variable. This way we can be sure that only
	one connection to the database can exists in the same time*/
	public static function init(array $dbOptions){
		if(self::$instance instanceof self){
			return false;
		}

		self::$instance = new self($dbOptions);
	}
	/*Retrives the SQLi object*/
	public static function getMySQLiObject(){
		return self::$instance->MySQLi;
	}
	/*Returns the result of the sql query*/
	public static function query($q){
		return self::$instance->MySQLi->query($q);
	}
	/*Escapes special characters in a string for use in an SQL statement,
	in this case: htmlspecialchars — That convert special characters to HTML entities*/
	public static function esc($str){
		return self::$instance->MySQLi->real_escape_string(htmlspecialchars($str));
	}
}

?>
