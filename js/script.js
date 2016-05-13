$(document).ready(function(){
  chat.init();
});

var chat = {
//data holds variables for use in the class
  data : {
    lastID  : 0,
    noActivity  : 0
  },

  //Init binds event listeners and sets up timers:

  init : function(){
    // Using the defaultText jQuery plugin, included at the bottom:
    $('#name').defaultText('Nickname');
    $('#email').defaultText('Email (Gravatas are enabled)');

    //converting the #chatLineHolder div into a jScrollPane,
    // and saving the plugin's API in chat.data:

    chat.data.jspAPI = $('#chatLineHolder').jScrollPane({
      verticalDragMinHeight: 12,
      verticalDragMaxHeight: 12
    }).data('jsp');

    //We use the working variablesto prevent
    //multiple from submissions:

    var working = false;

    //Logging a person in the chat:

    $('#loginForm').submit(function(){
      if(working) return false;
      working = true;

      //Using tzPost wrapper function
      //defined in the bottom:
      $.tzPOST('login',$(this).serialaize(),function(r){
        working = false;
        if(r.error){
          chat.displayError(r.error);
        }
        else chat.login(r.name, r.gravatar);
      });
      return false;
    });

    //Submitting a new chat entry
    $('#submitForm').submit(function(){
      var text = $('#chatText').val();
      if(text.length == 0){
        return false;
      }
      if(working) return false;
      working = true;

      //Assigning a remporary ID to the chat:
      var tempID ='t'+Math.round(Math.random()*1000000),
        parmas = {
          id  : tempID,
          author  : chat.data.name,
          gravatar  : chat.data.gravatar,
          text  : text.replace(/</g,'&lt;').replace(/>/g,'&gt;')
        };
        //Using addChatLine method to add the chat
        // to the screen immediately, without waiting
        // for the AJAX request to complete:
      chat.addChatLine($.extend({}, parmas));

      //Using tzPOST wrapper method to send the chat
      //via a POST AJAX request:

      $.tzPost('submitChat', $(this).serialize(),function(r){
        working = false;
        $('#chatText').val('');
        $('div.chat-'+tempID).remove();
        params['id'] = r.insertID;
        chat.addChatLine($.extend({},parmas));
      });
      return false;
    });

    // Logging the user out:
    $('a.logoutButton').live('click', function(){

      $('#chatTopBar > span').fadeOut(function(){
                $(this).remove();
            });

            $('#submitForm').fadeOut(function(){
                $('#loginForm').fadeIn();
            });

            $.tzPOST('logout');

            return false;
    });

    // Checking if the user is already logged

    $.tzGet('checkLogged', function(r){
      if(r.logged){
        chat.login(r.loggedAs.name, r.loggedAs.gravatar);
      }
    });

    //Self executing time out functions

    (function getChatsTimeoutFunction(){
      chat.getChats(getChatsTimeoutFunction);
    })();

    (function getUsersTimeOutFunction(){
      chat.getUsers(getUsersTimeOutFunction);
    })();
  },

}
