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

		$playerResult = mysqli_query($con,"SELECT * FROM Players WHERE Email = '$user'");
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

	<div class="sideBar clearfix">
		<h3>Admin Actions</h3>
		<a class="dashboardAction" href="#">Clear Ladder</a>
		<a class="dashboardAction" href="#">Start New Ladder Season</a>
		<a class="dashboardAction" href="#">Start New League Season</a>
		<a class="dashboardAction" href="#">Remove a Match</a>
		<a class="dashboardAction" href="#">Remove a Player</a>
	</div>

	<div class="primaryContent">
		<h3>Recent Activity</h3>
	</div>

</div>

<?php include_once('footer.php') ?>