<?php

session_start();

include 'ChromePhp.php';
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
		$userStatus['enrolled'] == true;
	} else {
		ChromePhp::log($playerList);
		ChromePhp::log($_POST['userEmail']);
		$userStatus['enrolled'] == false;
	}

	echo json_encode($userStatus);

	mysqli_close($con);

}

if ($_POST['name']) {
	mysqli_query($con, "INSERT INTO Players (Name, Email) VALUES ('$_POST[name]','$_POST[email]')");
	mysqli_close($con);
	header("Location: dashboard.php");
}

?>