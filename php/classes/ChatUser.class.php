<?php
  class ChatUser extends ChatBase{

    protected $name = '', $gravatar = '';

    /* A save method, which the object to our database.
    As it returns the MySQLi object, contained in the DB class,
    you can check whether the save was successful by checking
    the affected_rows property*/
    public function save(){
      DB::query("
        INSERT INTO webchat_users (name, gravatar)
        VALUES ( '".DB::esc($this->name)."',
        '".DB::esc($this->gravatar)."')
      ");

      return DB:getMySQLiObject();
    }

    /*updates the last_activity timestamp to the current time.
    This shows that this person keeps a chat window open and
    is displayed as online in the users section.*/
    public function update(){
      DB::query("
        INSERT INTO webchat_users (name, gravatar)
        VALUES ( '".DB::esc($this->name)."',
        '".DB::esc($this->gravatar)."')
        ON DUPLICATE KEY UPDATE last_activity = NOW()
      ");

    }
  }
?>
