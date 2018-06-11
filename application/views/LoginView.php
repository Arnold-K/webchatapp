
<?php
    include APP . "views/__templates/__variables.php";  //include all variables needed for the pages
?>
<!DOCTYPE html>
<html>
<head>
    <?php 
        $pageTitle .= "Login";
        include APP . "views/__templates/__header.php";     //add some headers only
        echo $bootstrap_css;                                //bootstrap variable
        echo $user_css;                                    //login.css only
    ?>
</head>
<body>
    
    <div class="container">
        <div class="card card-container">
            <!-- <img class="profile-img-card" src="//lh3.googleusercontent.com/-6V8xOA6M7BA/AAAAAAAAAAI/AAAAAAAAAAA/rzlHcD0KYwo/photo.jpg?sz=120" alt="" /> -->
            <img id="profile-img" class="profile-img-card" src="//ssl.gstatic.com/accounts/ui/avatar_2x.png" />
            <p id="profile-name" class="profile-name-card"></p>
            <form class="form-signin" method="post">
                <span id="reauth-email" class="reauth-email"></span>
                <input name="username" type="text" id="inputEmail" class="form-control" placeholder="Email address" required autofocus autocomplete="off">
                <input name="password" type="password" id="inputPassword" class="form-control" placeholder="Password" required autocomplete="off">
                <div id="remember" class="checkbox">
                    <label>
                        <input type="checkbox" value="remember-me" name="rememeber"> Remember me
                    </label>
                </div>
                <button class="btn btn-lg btn-primary btn-block btn-signin" type="submit">Sign in</button>
            </form><!-- /form -->
            <a href="/webchatapp/signup" class="forgot-password">
                Don't have an account?
            </a>
            <p class="btn-danger" data-message="login"></p>
        </div><!-- /card-container -->
    </div><!-- /container -->


    <?php
        echo    $jquery_js .
                $bootstrap_js .
                $user_js;
    ?>
</body>
</html>

