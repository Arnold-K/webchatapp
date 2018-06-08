/* -- SIGN IN -- */
$('.form-signin').on("submit", function(event){
    event.preventDefault();
    var formData = $('.form-signin').serializeArray();
    $.post( "/webchatapp/api/user/signin", formData , function(data) {
        if(data.code == 404){
            console.log("user not found");
            $("p[data-message]").text("Credential combination missmatch!");
            return false;
        }
        $("p[data-message]").text("");
        let id          = data.id
        let email       = data.email;
        let last_login  = data.last_login_at;
        let name        = data.name;
        let role        = data.role;
        let updated_at  = data.updated_at;
        let username  = data.username;
    })
        .done(function() {

        })
        .fail(function() {
            $("p[data-message]").text("Server is under construction!");
        })
        .always(function() {

        });
});

/* -- SIGN UP -- */
$(".signup-button").on("click", function(event){
    var formData = $("form").serializeArray();
    console.log(formData);
    $.post( "/webchatapp/api/user/signup", formData , function(data) {

    });
});