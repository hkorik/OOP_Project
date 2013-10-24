<?php

	include("connection.php");

	if(isset($_SESSION['logged_in']))
	{
		header("Location: home.php");
	}

?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Log in - Registration Page</title>
	<link rel="stylesheet" type="text/css" href="CSS/styles.css" />
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
</head>
<body>
	<div id="wrapper">
		<h1>Log in and Registration Page</h1>
		<!-- Registration left box -->
		<div id="registration_box" class="float_left">
			<h2>Register</h2>
			<?php
				if(isset($_SESSION['register_errors']))
				{
					foreach($_SESSION['register_errors'] as $error)
					{
						echo "<p class='error_message'>$error</p>";
					}
				}

				else if(isset($_SESSION['register_message']))
				{
					echo "<p class='success_message'>{$_SESSION['register_message']}</p>";
					unset($_SESSION['register_message']);
				}
			?>
			<form action="process.php" method="post">
				<input type="hidden" name="action" value="register" />
				<div class="<?php if(isset($_SESSION['register_errors']['first_n_error'])) echo 'field_block'; ?>">
					<label for="first_name">First Name *</label><br/>
					<input type="text" name="first_name" id="first_name" placeholder="First Name" />
				</div>
				<div class="<?php if(isset($_SESSION['register_errors']['last_n_error'])) echo 'field_block'; ?>">
					<label for="last_name">Last Name *</label><br/>
					<input type="text" name="last_name" id="last_name"placeholder="Last Name" />
				</div>
				<div class="<?php if(isset($_SESSION['register_errors']['email_error'])) echo 'field_block'; ?>">
					<label for="email">Email *</label><br/>
					<input type="text" name="email" id="email" placeholder="Email" />
				</div>	
				<div class="<?php if(isset($_SESSION['register_errors']['pw_error'])) echo 'field_block'; ?>">
					<label for="password">Password *</label><br/>
					<input type="password" name="password" id="password" placeholder="Password" />
				</div>
				<input class="btn btn-primary" type="submit" value="Register" />
			</form>
			<?php unset($_SESSION['register_errors']); ?>
		</div>
		<!-- Login right box -->
		<div id="login_box" class="float_right">
			<h2>Login</h2>
			<?php
				if(isset($_SESSION['login_errors']))
				{
					foreach($_SESSION['login_errors'] as $error)
					{
						echo "<p class='error_message'>$error</p>";
					}
				}

				else if(isset($_SESSION['message']))
				{
					
				}
			?>
			<form action="process.php" method="post">
				<input type="hidden" name="action" value="login" />
				<div class="<?php if(isset($_SESSION['login_errors']['email_error'])) echo 'field_block'; ?>">
					<label for="email">Email *</label><br/>
					<input type="text" name="email" id="email" placeholder="Email" />
				</div>	
				<div class="<?php if(isset($_SESSION['login_errors']['pw_error'])) echo 'field_block'; ?>">
					<label for="password">Password *</label><br/>
					<input type="password" name="password" id="password" placeholder="Password" />
				</div>
				<input class="btn btn-primary" type="submit" value="Login" />
			</form>
			<?php unset($_SESSION['login_errors']); ?>
		</div>
	</div>
</body>
</html>