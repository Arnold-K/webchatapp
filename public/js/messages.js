$(".logout").on("click", logout);
$(".deleteprofile").on("click", deleteProfile);
$(".send-message").on("click", sendMessage);
$(document).ready(loadMessages());
$(".send-message-view").on("click", sendMessage2);
$("body > div.main_section > div > div > div.col-sm-9.message_section > div > div.new_message_head > div.pull-right > div > ul > li:nth-child(1) > a").on("click", deleteMessages);

let current_id = 0;


function logout(){
    $.get("/webchatapp/api/user/signout", function(data){
        if(data.url){
            window.location.assign(data.url);
            console.log(data.url);
        }
    });
}

function deleteProfile(){
    $.get("/webchatapp/api/user/deleteuser", function(data){
        if(data.url){
            window.location.assign(data.url);
        }
    });
}

function sendMessage(){
    let receiver = $('input[name="username"]').val();
    let content = $('[name="newmessagecontent"]').val();
    let formData = {"username" : receiver, "messagecontent" : content}
    $.post( "/webchatapp/api/messages/sendmessage", formData , function(data) {
        location.reload();
    })
}

function loadMessages(){
    $.get("/webchatapp/api/messages/getMessagesNames", function(data){
        for (let i = 0; i < data.length; i++) {
            let html_obj = '<li class="left clearfix"><div class="chat-body clearfix"><div class="header_sec">';
            html_obj += '<strong class="primary-font user-messages" data-user="'+data[i].id+'">'+data[i].name+'</strong>';
            html_obj += '</div></div></li>';
            $('#messages_list').append(html_obj);
        }
        $(".user-messages").on("click", loadMessagesUser);
        $(".user-messages").eq(0).click();
        current_id = 0+1;
    });
}

function loadMessagesUser(){
    let id = this.getAttribute("data-user");
    current_id = id;
    $.get("/webchatapp/api/messages/getMessagesUser/"+id, function(data){
        $('.user-mess').empty();
        for (let i = 0; i < data.length; i++) {
            let html_obj = '<li class="left clearfix"><div class="chat-body1 clearfix"><p>';
            html_obj += data[i].message;
            html_obj += '</p></div></li>';
            $('.user-mess').append(html_obj);
        }
    });
}

function deleteMessages(){
    let formData = {"id" : current_id}
    $.post( "/webchatapp/api/messages/deleteMessages", formData);
    location.reload();
}

function sendMessage2(){
    let message = $('#msg-content').val();
    console.log(message);
    console.log(current_id);
    let formData = {"id" : current_id, "messagecontent" : message}
    $.post( "/webchatapp/api/messages/sendmessagebyid", formData , function(data) {
        $.get("/webchatapp/api/messages/getMessagesUser/"+current_id, function(data){
            $('.user-mess').empty();
            for (let i = 0; i < data.length; i++) {
                let html_obj = '<li class="left clearfix"><div class="chat-body1 clearfix"><p>';
                html_obj += data[i].message;
                html_obj += '</p></div></li>';
                $('.user-mess').append(html_obj);
            }
        });
    })
    
}

