<?php
/* This class is used for the chat entries */

  class ChatLine extends ChatBase{
    /* $text == text
       $author == the person who wrote the textmessage
       $gravatar ==  contains a md5 hash of the person’s email address.
       This is required so we can fetch the user’s gravatar from gravatar.com
    */
    protected $text = '', $author = '', $gravatar = '';

    /* A save method, which the object to our database.
    As it returns the MySQLi object, contained in the DB class, you can check
    whether the save was successful by checking the affected_rows property*/
    public function save(){
       DB::query("
           INSERT INTO webchat_lines (author, gravatar, text)
           VALUES (
               '".DB::esc($this->author)."',
               '".DB::esc($this->gravatar)."',
               '".DB::esc($this->text)."'
       )");
       // Returns the MySQLi object of the DB class.
       return DB::getMySQLiObject();
     }
  }
 ?>
