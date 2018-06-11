/* -- SIGN IN -- */
var formData
$('.form-signin').on("submit", function(event){
    event.preventDefault();
    formData = $('.form-signin').serializeArray();
    if(formData[0].value.length<5){
        $("p[data-message]").text("Username: more than 5 letters.");
        return;
    }
    if(formData[1].value.length<8){
        $("p[data-message]").text("Password: more than 7 letters.");
        return;
    }
    
    $.post( "/webchatapp/api/user/signin", formData , function(data) {
        if(data.code == 404){
            console.log("user not found");
            $("p[data-message]").text("Credential combination missmatch!");
            return false;
        }
        $("p[data-message]").text("");
        // let id          = data.id
        // let email       = data.email;
        // let last_login  = data.last_login_at;
        // let name        = data.name;
        // let role        = data.role;
        // let updated_at  = data.updated_at;
        // let username    = data.username;
        if(data.url){
            window.location.assign(data.url);
        }
    })
});

$(document).ready(function(){
    $.get("/webchatapp/api/user/status", function(data){
        if(data.url){
            window.location.assign(data.url);
        }
    });
});

/* -- SIGN UP -- */
$(".signup-button").on("click", function(event){
    var formData = $("form").serializeArray();
    console.log(formData);
    $.post( "/webchatapp/api/user/signup", formData , function(data) {

    });
});
