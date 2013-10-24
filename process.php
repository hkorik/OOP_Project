<?php

include("connection.php");
class Process{

	var $run_database;
	var $html;
	var $table1;

	function __construct()
	{
		$this->run_database = new Database();

		//When user registers - run register function
		if(isset($_POST['action']) and $_POST['action'] == "register")
		{
			$this->register_action();
		}
		//When user logs in - run login function
		else if (isset($_POST['action']) and $_POST['action'] == "login") 
		{
			$this->login_action();
		}
		elseif(isset($_POST['action']) and $_POST['action'] == "friends")
		{
			$this->get_friends_list();
			$data = $this->table1;
			echo json_encode($data);
		}
		elseif(isset($_POST['action']) and $_POST['action'] == "users")
		{
			$this->get_users_list();
			$data = $this->html;
			echo json_encode($data);
		}
		else if(isset($_POST['action']) and $_POST['action'] == "users_id")
		{
			$this->add_friend();
			$added = "added friend";
			echo json_encode($added);
		}
		else
		{
			session_destroy();
			header("Location: login.php");
		}
	}
	
	function register_action()
	{	
		$errors = NULL;

		//Email validation
		if(empty($_POST['email']))
		{
			$errors['email_error'] = "Error: Email address cannot be blank!";
		}
		else if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
		{
			$errors['email_error'] = "Error: Email should be in valid format!";
		}

		//First name validation
		if(empty($_POST['first_name']))
		{
			$errors['first_n_error'] = "Error: First name field cannot be blank!";
		}
		else if (preg_match('#[0-9]#', $_POST['first_name'])) 
		{
			$errors['first_n_error'] = "Error: First name cannot contain numbers!";
		}

		//Last name validation
		if(empty($_POST['last_name']))
		{
			$errors['last_n_error'] = "Error: Last name field cannot be blank!";
		}
		else if (preg_match('#[0-9]#', $_POST['last_name'])) 
		{
			$errors['last_n_error'] = "Error: Last name cannot contain numbers!";
		}

		//Password validation
		if(empty($_POST['password']))
		{
			$errors['pw_error'] = "Error: Password field cannot be blank!";
		}
		else if(strlen($_POST['password']) < 6)
		{
			$errors['pw_error'] = "Error: Password must be greater than 6 charecters";
		}

		//if there are any errors
		if(count($errors) > 0)
		{
			$_SESSION['register_errors'] = $errors;
			header("Location: login.php");
		}

		// if everything is correct!
		else
		{
			//check if user email exists
			$check_user = "SELECT * FROM users WHERE email = '{$_POST['email']}'";
			$user_exist = $this->run_database->fetch_record($check_user);

			// if no one has that email address
			if($user_exist == NULL)
			{
				$new_user = "INSERT INTO users (first_name, last_name, email, password, created_at) VALUES ('{$_POST['first_name']}','{$_POST['last_name']}','{$_POST['email']}','" . md5($_POST['password']) . "', now() )";

				mysql_query($new_user);

				$check_user_info = "SELECT * FROM users WHERE email = '{$_POST['email']}' AND password = '" . md5($_POST['password']) . "'";

				$user_info = $this->run_database->fetch_record($check_user_info);

				$_SESSION['user']['id'] = $user_info['id'];
				$_SESSION['user']['first_name'] = $user_info['first_name'];
				$_SESSION['user']['last_name'] = $user_info['last_name'];
				$_SESSION['user']['email'] = $user_info['email'];
				$_SESSION['logged_in'] = TRUE;

				if($_SESSION['logged_in'] == TRUE)
				{
					header("Location: home.php?=" . $_SESSION['user']['id']);
				}
				
			}
			// if email already exists
			else
			{
				$errors['email_error'] = "Error: Email {$_POST['email']} is already in use!";
				$_SESSION['register_errors'] = $errors;
				header("Location: login.php");
			}
		}
	}

	//login button function
	function login_action()
	{	
		$errors_login = NULL;

		//Email validation
		if(empty($_POST['email']))
		{
			$errors_login['email_error'] = "Error: Email address cannot be blank!";
		}
		else if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
		{
			$errors_login['email_error'] = "Error: Email should be in valid format!";
		}

		//Password validation
		if(empty($_POST['password']))
		{
			$errors_login['pw_error'] = "Error: Password field cannot be blank!";
		}
		else if(strlen($_POST['password']) < 6)
		{
			$errors_login['pw_error'] = "Error: Password length must be at least 6 charecters!";
		}

		//if there are any error
		if(count($errors_login) > 0)
		{
			$_SESSION['login_errors'] = $errors_login;

			header("Location: login.php");
		}

		// if everything is correct!
		else
		{
			$check_user_info = "SELECT * FROM users WHERE email = '{$_POST['email']}' AND password = '" . md5($_POST['password']) . "'";
			
			$user_info = $this->run_database->fetch_record($check_user_info);
			
			if($user_info != NULL)
			{
				$_SESSION['user']['id'] = $user_info['id'];
				$_SESSION['user']['first_name'] = $user_info['first_name'];
				$_SESSION['user']['last_name'] = $user_info['last_name'];
				$_SESSION['user']['email'] = $user_info['email'];
				$_SESSION['logged_in'] = TRUE;

				if($_SESSION['logged_in'] == TRUE)
				{
					header("Location: home.php?=" . $_SESSION['user']['id']);	
				}

			}
			else
			{
				$errors[] = "Error: The information entered does not match any of our records!";
				$_SESSION['login_errors'] = $errors;
				header("Location: login.php");
			}	
		}
	}

	function get_users_list()
	{
		$friend_status = array();

		// query to get all the friends
		$friends_query = "SELECT * FROM friends";
		$friends = $this->run_database->fetch_all($friends_query);

		// check if the current user's id exists in the friends table
		foreach($friends as $friend)
		{
			if($_SESSION['user']['id'] == $friend['user_id'])
			{
				$friend_status[$friend['friend_id']] = TRUE;
			}
		}

		// store it in a another array $friend_status
		$users_query = "SELECT id, first_name, last_name, email
				  FROM users
				  WHERE users.id != '{$_SESSION['user']['id']}'";
		$users_list = $this->run_database->fetch_all($users_query);

		foreach($users_list as $user)
		{
			$this->html .= "<tr>";
			$this->html .= "<td>{$user['first_name']} {$user['last_name']}</td>
								<td>{$user['email']}</td>";

			if(isset($friend_status[$user['id']]))
			{
				$this->html .= "<td>Friends</td>";
			}
			else
			{
				$this->html .= "<td>
									<form class='add_friend_form' action='process.php' method='post'>
									<input type='hidden' name='action' value='users_id'>
									<input type='hidden' name='user_id' value='{$user['id']}'>
									<input class='btn btn-success' type='submit' value='Add as Friend' />
									</form>
								</td>";
			}

			$this->html .= "</tr>";				
		}
	}

	function get_friends_list()
	{
		$query = "SELECT users.first_name, users.last_name, users.email
				FROM users
				LEFT JOIN friends
				ON friends.friend_id = users.id
				WHERE friends.user_id = '{$_SESSION['user']['id']}'";

		$friends_list = $this->run_database->fetch_all($query);

		foreach($friends_list as $friend)
		{
			$this->table1 .= "<tr>
								<td>{$friend['first_name']} {$friend['last_name']}</td>
								<td>{$friend['email']}</td></tr>";
		}
	}

	function add_friend()
	{
		$query = "INSERT INTO friends (user_id, friend_id) VALUES ('{$_SESSION['user']['id']}', '{$_POST['user_id']}'), ('{$_POST['user_id']}', '{$_SESSION['user']['id']}')";

		mysql_query($query);
	}
}

$process = new Process();

?>