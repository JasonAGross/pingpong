<?php

	session_start();

	if ($_SESSION['status'] === false) {
		header("Location: index.html");
		die();
	}
	
	include 'functions.php';
	
	$playerInfo = array();
	$matchInfo = array();

	if ($_SESSION['user']) {
		$user = $_SESSION['user'];
		$con=mysqli_connect("localhost", $dbUser, $dbPass, $dbTable);

		$playerResult = mysqli_query($con,"SELECT * FROM Players");
		while($row = mysqli_fetch_assoc($playerResult)) {
			$playerInfo = $row;
		}

		$matchResult = mysqli_query($con,"SELECT * FROM Games ORDER BY PlayedOn DESC");
		while($row = mysqli_fetch_assoc($matchResult)) {
			$matchInfo[] = $row;
		}
	}
	
	if ($playerInfo['Access'] != 2) {
		header("Location: dashboard.php");
		die();
	}

?>

<?php include_once('header.php') ?>

<div class="wrapper admin clearfix">
	
	<h1>Admin Tools</h1>
</div>

<?php include_once('footer.php') ?>