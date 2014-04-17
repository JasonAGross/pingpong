<?php

session_start();

include 'config.php';

if ($_POST['status']['signed_in']) {
	$userStatus = array();
	$_SESSION['status'] = $_POST['status']['signed_in'];
	$_SESSION['user'] = $_POST['userEmail'];
	$_SESSION['userName'] = $_POST['userName'];

	$userStatus['user'] = $_POST['userEmail'];
	$userStatus['userName'] = $_POST['userName'];
	
	global $dbUser, $dbPass, $dbTable;
	$con=mysqli_connect("127.0.0.1", $dbUser, $dbPass, $dbTable);

	// Check connection
	if (mysqli_connect_errno())	{
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$playerList = array();
	$result = mysqli_query($con,"SELECT Email FROM Players");
	while($row = mysqli_fetch_array($result)) {
		$playerList[] = $row['Email'];
	};

	if (in_array($_POST['userEmail'], $playerList)) {
		$userStatus['enrolled'] = true;
	} else {
		$userStatus['enrolled'] = false;
	}

	echo json_encode($userStatus);

	mysqli_close($con);

}

if ($_POST['name']) {
	global $dbUser, $dbPass, $dbTable;
	$con=mysqli_connect("127.0.0.1", $dbUser, $dbPass, $dbTable);

	$globalResult = mysqli_query($con,"SELECT LadderSeason FROM Globals");
	$row = mysqli_fetch_assoc($globalResult);
	$ladder = $row['LadderSeason'];

	mysqli_query($con, "INSERT INTO Players (Name, Email, LadderSeason) VALUES ('$_POST[name]','$_POST[email]','$ladder')");
	mysqli_close($con);
	header("Location: dashboard.php");
}

?>