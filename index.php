<?php include_once('header.php') ?>

<?php

	$con=mysqli_connect("localhost", $dbUser, $dbPass, $dbTable);

	// Check connection
	if (mysqli_connect_errno())	{
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	// Pull registered users to see if this user has an account or needs to sign up
	$result = mysqli_query($con,"SELECT Email FROM Players");
	$row = mysqli_fetch_array($result);
?>

<script type="text/javascript">
	var emails = [<?php print_r(json_encode($row[Email])) ?>];
</script>

<?php

    if('POST' == $_SERVER['REQUEST_METHOD']) {

		mysqli_query($con, "INSERT INTO Players (Name, Email) VALUES ('$_POST[name]','$_POST[email]')");

        mysqli_close($con);
		header("Location: dashboard.php");
		die();
    }

?>

<div class="wrapper loginPage">
	<h1>Healthx Ping Pong League</h1>

	<div class="auth">
		<h3>Login</h3>
		<p>Welcome to the Healthx ping pong league and ladder system. Please login to proceed to your dashboard.</p>
		<button class="btn" id="googleAuth">Login</button>
	</div>

	<div class="register" style="display: none;">
		<h3>Complete Signup</h3>
		<img id="userImg" src="#" />
		<p>Hey <span id="fName"></span>, we don't have any matches for this account in our system. If you have already registered please switch accounts. Otherwise, please confirm your name and email address and we'll get an account set up for you.</p>

		<div class="registrationForm">
			<form name="registration" action="" method="POST">
				<label for="name">Full Name</label>
				<input type="text" name="name" id="regName" />
				<label for="email">Email Address</label>
				<input type="email" name="email" id="regEmail" />
				<input type="submit" value="Submit" class="btn" />
			</form>
		</div>
	</div>

</div>

<?php include_once('footer.php') ?>