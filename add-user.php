<?php session_start(); ?>
<?php require_once('inc/connection.php'); ?>
<?php require_once('inc/functions.php'); ?>
<?php 
	// checking if a user is logged in
	if (!isset($_SESSION['user_id'])) {
		header('Location: index.php');
	}

	$errors = array();
	$first_name = '';
	$last_name = '';
	$email = '';
	$password = '';

	if (isset($_POST['submit'])) {

		$first_name = $_POST['first_name'];
		$last_name = $_POST['last_name'];
		$email = $_POST['email'];
		$password = $_POST['password'];

		$required_fields = array('first_name', 'last_name', 'email', 'password');
		// checking required fields
		$errors = array_merge($errors, check_req_fields($required_fields));

		// checking max length
		$max_len_fields = array('first_name' => 100, 'last_name'=> 100, 'email' => 100, 'password' => 8);
		$errors = array_merge($errors, check_max_length($max_len_fields));

		// checking email
		if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
			//Email is not good
			$errors[] = "email address is invalid";
		}

		// checking email address already exists
		$email = mysqli_real_escape_string($connection, $_POST['email']);
		$query = "SELECT * FROM user WHERE email = '{$email}' LIMIT 1";

		$result_set = mysqli_query($connection, $query);
		if ($result_set) {
			if (mysqli_num_rows($result_set) == 1) {
				$errors[] = "Email address already exists";
			}
		}

		if (empty($errors)) {
			// no errors found adding new record
			$first_name = mysqli_real_escape_string($connection, $_POST['first_name']);
			$last_name = mysqli_real_escape_string($connection, $_POST['last_name']);
			$password = mysqli_real_escape_string($connection, $_POST['password']);
			// email address alredy sanitized

			$query = "INSERT INTO user (";
			$query .= "first_name, last_name, email, password, is_deleted";
			$query .= ") VALUES (";
			$query .= "'{$first_name}', '{$last_name}', '{$email}', '{$password}', 0";
			$query .= ")";

			$result = mysqli_query($connection, $query);

			if ($result) {
				// query successful.. redirecting to users page
				header('Location: users.php?user_added=true');
			} else {
				$errors[] = "Failed to add new record.";
			}
		}
	}


 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>New User</title>
	<link rel="stylesheet" href="css/main.css">
</head>
<body>

	<header>
		<div class="appname">User Mangement System</div>
		<div class="loggedin">Welcome <?php echo $_SESSION['first_name']; ?>! <a href="logout.php">Logout</a></div>
	</header>
	
	<main>
		<h1>Add New User <span><a href="users.php">< Back to user List</a></span></h1>

		<?php 
			if (!empty($errors)) {
				display_errors($errors);
			}
		?>

		<form action="add-user.php" class="userform" method="post">
			
			<p>
				<label for="">First Name:</label>
				<input type="text" name="first_name" <?php echo 'value="' .$first_name . '"'; ?>>
			</p>

			<p>
				<label for="">Last Name:</label>
				<input type="text" name="last_name" <?php echo 'value="' .$last_name . '"'; ?>>
			</p>

			<p>
				<label for="">Email:</label>
				<input type="text" name="email" <?php echo 'value="' .$email . '"'; ?>>
			</p>

			<p>
				<label for="">New Password:</label>
				<input type="password" name="password">
			</p>

			<p>
				<label for="">&nbsp;</label>
				<button type="submit" name="submit">Save</button>
			</p>

		</form>
	</main>
	
</body>
</html>