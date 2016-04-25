<?php
/* This is the base class, used by both ChatLine and ChatUser */
/*It’s main purpose is to define the constructor, which takes an
array with parameters, and saves only the ones that are defined
in the class.*/
class ChatBase{

  // This constructor is used by all the chat classes:
  public function __construct(array $options){
    foreach ($option as $key => $value) {
      if(isset($this->$key)){
        $this->$key = $value;
      }
    }
  }
}
 ?>
