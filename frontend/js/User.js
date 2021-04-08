var $active_user;
var $active_user_name = '';
var $room_code = '';

$(document).ready(function() {

    var data = JSON.stringify({
        "limit":1,
        "fields": [
            "chat_room_code",
            "chat_room_name"
        ]
    });

    setInterval(function(){
        fetchMessage();
    }, 4000);

    $.ajax({
        url: "http://localhost:8080/chat/select-chat-rooms",
        type: "POST",
        data: data,
        dataType:"json",
        cache: false,
        async: true,
        processData: true,
        success: function (response) {
            if (response.content.data.length > 0) {
                $room_code = response.content.data[0].chat_room_code;
            }
        },
        error: function (response) { 
          console.log(response.data);
        }
    });

    $.ajax({
        url: "http://localhost:8080/chat/select-users",
        type: "POST",
        dataType:"json",
        cache: false,
        async: true,
        processData: true,
        success: function (response) {
            response.content.data.forEach(function($el){
                $('.users').append(
                    "<a href='#' data-user_code='"+ $el.user_code + "' data-user_name='"+ $el.user_name +"' class='list-group-item list-group-item-action'><b>" + $el.user_name + "</b></a>"
                )
            });
        },
        error: function (response) { 
          console.log(response.data);
        }
    });

    $(document).on("click", ".users a" , function() {
        $active_user = $(this).attr("data-user_code");
        $active_user_name = $(this).attr("data-user_name");
        $('.users .active').removeClass('active');
        $(this).addClass('active');
    });

        // //-- Clear Chat
        // resetChat();
        // //-- Print Messages;

    $(".mytext").on("keyup", function(e) {

        if (!$active_user) {
            alert("Please Select A User.");
            return;
        }

        if (e.which == 13){

            var text = $(this).val();
            if (text !== "") {

                insertChat($active_user_name, text);      
                createMessage(text);
                $(this).val('');
            
            }
        }
    });  

    function createMessage(text) 
    {
        $.ajax({
            url: "http://localhost:8080/chat/create-message",
            type: "POST",
            data: {
                "message_chat_room_code": $room_code ,
                "message_content": text,
                "message_created_by": $active_user
            },
            dataType:"json",
            cache: false,
            async: true,
            processData: true,
            success: function (response) {
               
            },
            error: function (response) { 
              console.log(response.data);
            }
        });
    }

    function fetchMessage()
    {
        var data = {
            "fields": [
              "message_id",
              "message_code",
              "message_chat_room_code",
              "message_content",
              "message_status",
              "chat_room_code",
              "chat_room_name",
              "chat_room_status",
              "user_id",
              "user_code",
              "user_name",
              "user_status"
            ],
            "join": [
              {
                "tableWith": "user",
                "columnWith": "user_code",
                "columnBy": "message_created_by"
              },
              {
                "tableWith": "chat_room",
                "columnWith": "chat_room_code",
                "columnBy": "message_chat_room_code"
              }
            ]
          };

        $.ajax({
            url: "http://localhost:8080/chat/get-messages",
            type: "POST",
            data: data,
            dataType:"json",
            cache: false,
            async: true,
            processData: true,
            success: function (response) {
                if (response.content.data.length > 0) {
                    resetChat();
                    response.content.data.forEach(function($el){
                        insertChat($el.user_name, $el.message_content)
                        console.clear();
                    });
                }
            },
            error: function (response) { 
              console.log(response.data);
            }
        });
    }

    
});