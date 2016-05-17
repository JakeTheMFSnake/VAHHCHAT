<?php

/* Chat line is used for the chat entries */

class ChatLine extends ChatBase{
	/* $text == text
       $author == the person who wrote the textmessage
			 
    */
	protected $text = '', $author = '';
	/* A save method, which the object to our database.
    As it returns the MySQLi object, contained in the DB class, you can check
    whether the save was successful by checking the affected_rows property*/
	public function save(){
		DB::query("
			INSERT INTO webchat_lines (author, text)
			VALUES (
				'".DB::esc($this->author)."',

				'".DB::esc($this->text)."'
		)");

		// Returns the MySQLi object of the DB class

		return DB::getMySQLiObject();
	}
}

?>
