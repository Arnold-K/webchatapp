$(".logout").on("click", logout);
$(".deleteprofile").on("click", deleteProfile);
$(".send-message").on("click", sendMessage);


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
            console.log(data.url);
        }
    });
}

function sendMessage(){
    let receiver = $('input[name="username"]').val();
    let content = $('[name="newmessagecontent"]').val();
    let formData = {"username" : receiver, "messagecontent" : content}
    $.post( "/webchatapp/api/messages/sendmessage", formData , function(data) {

    })
}