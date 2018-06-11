
<?php
    include APP . "views/__templates/__variables.php";  //include all variables needed for the pages
?>
<!DOCTYPE html>
<html>
<head>
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
    <?php 
        $pageTitle .= "Sign Up";
        include APP . "views/__templates/__header.php";     //add some headers only                      
			echo $user_css;                                    
	?>
	
</head>
<body>
    
	<div class="container">
		<div class="row main">
			<div class="panel-heading">
				<div class="panel-title text-center">
					<h1 class="title">Register to start chatting...</h1>
					<hr />
				</div>
			</div> 
			<div class="main-login main-center">
				<form class="form-horizontal sign-up" method="post">
					
					<div class="form-group">
						<label for="name" class="cols-sm-2 control-label">Your Name</label>
						<div class="cols-sm-10">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-user fa" aria-hidden="true"></i></span>
								<input type="text" class="form-control" name="name" id="name" autocomplete="off"  placeholder="Enter your Name"/>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label for="email" class="cols-sm-2 control-label">Your Email</label>
						<div class="cols-sm-10">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-envelope fa" aria-hidden="true"></i></span>
								<input type="text" class="form-control" name="email" id="email" autocomplete="o"  placeholder="Enter your Email"/>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label for="username" class="cols-sm-2 control-label">Username</label>
						<div class="cols-sm-10">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-users fa" aria-hidden="true"></i></span>
								<input type="text" class="form-control" name="username" id="username" autocomplete="off"  placeholder="Enter your Username"/>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label for="password" class="cols-sm-2 control-label">Password</label>
						<div class="cols-sm-10">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-lock fa-lg" aria-hidden="true"></i></span>
								<input type="password" class="form-control" name="password" id="password" autocomplete="off"  placeholder="Enter your Password"/>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label for="confirm" class="cols-sm-2 control-label">Confirm Password</label>
						<div class="cols-sm-10">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-lock fa-lg" aria-hidden="true"></i></span>
								<input type="password" class="form-control" name="confirm" id="confirm" autocomplete="off"  placeholder="Confirm your Password"/>
							</div>
						</div>
					</div>

					<div class="form-group ">
						<button type="button" class="btn btn-primary btn-lg btn-block signup-button">Register</button>
					</div>
					<div class="login-register">
						<a href="login">Already have an account?</a>
						</div>
				</form>
			</div>
		</div>
	</div>
	<!-- /container -->

    <?php
        echo    $jquery_js . 
				$bootstrap_js.
				$user_js;
    ?>
</body>
</html>

<script type="text/javascript">

</script>

