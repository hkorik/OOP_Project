<?php
	
	include("connection.php");
	
	if(!isset($_SESSION['logged_in']))
	{
		header("Location: login.php");
	}

?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Home Page</title>
	<link rel="stylesheet" type="text/css" href="CSS/styles.css" />
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
	<link rel="stylesheet" media="all" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/ui-lightness/jquery-ui.css" />
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){

			 $('#user_form').submit(function(){
                var form = $(this);
                $.post(
                    form.attr('action'),
                    form.serialize(),
                    function(data){
                        $('#users_list').html(data);
                    },
                     'json');
               return false;
            });
            
           $('#user_form').submit();

            $('#friend_form').submit(function(){
                var form = $(this);
                $.post(
                    form.attr('action'),
                    form.serialize(),
                    function(data){
                        $('#friends_list').html(data);
                    }, 
                    'json');
                return false;
            });
            
            $('#friend_form').submit();

			$(document).on('submit', '.add_friend_form', function(){
				var form = $(this);
				$.post(
					form.attr('action'),
					form.serialize(),
					function(data){
						$('#user_form').submit();
	                    $('#friend_form').submit();
					},
					'json');
				return false;
			});
			
		});

	 </script>
</head>
<body>
	<div id="wrapper_home">
		<div id="header">
			<p class="float_left">Welcome <?php echo "{$_SESSION['user']['first_name']}" ?>!</p>
			<p class="float_right"><a class="btn btn-danger" href="process.php">Log off</a></p>
			<p class="clear"><?php echo "{$_SESSION['user']['email']}" ?></p>
		</div>
		<div id="main">
			<h3>List of Friends</h3>
			<form id="friend_form" action="process.php" method="post">
				<input type="hidden" name="action" value="friends" />
			</form>
			<table class="table">
				<thead>
					<th>Name</th>
					<th>Email</th>
				</thead>
				<tbody id="friends_list">
					
				</tbody>
			</table>

			<h3>List of Users who subscribed to Friend Finder</h3>
			<form id="user_form" action="process.php" method="post">
				<input type="hidden" name="action" value="users" />
			</form>	
			<table class="table">
				<thead>
					<th>Name</th>
					<th>Email</th>
					<th>Action</th>
				</thead>
				<tbody id="users_list">
					
				</tbody>
			</table>
		</div>
	</div>
</body>
</html>