<?php
session_start();

// Make sure the user is logged in
if ($_SESSION['status'] === false) {
	header("Location: index.html");
	die();
}

include 'functions.php';

ChromePhp::log($_POST);

// Are we here to submit a challenge report?
if ($_POST['behavior'] == 'submitScore') {
	if ($_POST['playerScore'] + $_POST['opponentScore'] != 3) {
		echo 'Match score does not add up to 3 rounds. Please check scores and try again';
	} else {
		$matchID = $_POST['matchID'];
		$playedOn = date('Y-m-d');
		if ($_POST['matchRole'] == 'Challenge') {
			$challengerID = $_POST['player'];
			$challengerScore = $_POST['playerScore'];
			$defenderID = $_POST['opponent'];
			$defenderScore = $_POST['opponentScore'];
		} else {
			$challengerID = $_POST['opponent'];
			$challengerScore = $_POST['opponentScore'];
			$defenderID = $_POST['player'];
			$defenderScore = $_POST['playerScore'];
		}
		$con=mysqli_connect("localhost", $dbUser, $dbPass, $dbTable);
		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		mysqli_query($con, "UPDATE Games SET ChallengerScore = '$challengerScore', DefenderScore = '$defenderScore', Status = 'Complete', PlayedOn = '$playedOn' WHERE MatchID = '$matchID'");

		echo 'Success';

		mysqli_close($con);
	}
}

// Are we here to issue a challenge?
if ($_POST['behavior'] == 'issueChallenge') {
	if (!$_POST['player'] || !$_POST['opponent']) {
		echo 'Something went wrong. Please try again.';
	} else {
		global $dbUser, $dbPass, $dbTable;
		$con=mysqli_connect('localhost', $dbUser, $dbPass, $dbTable);

		// Get the current season
		$seasonResult = mysqli_query($con, "SELECT LadderSeason FROM Globals");
		$row = mysqli_fetch_assoc($seasonResult);
		$season = $row['LadderSeason'];

		mysqli_query($con, "INSERT INTO Games (SeasonID, ChallengerID, DefenderID, MatchType, Status) VALUES ('$season','$_POST[player]','$_POST[opponent]','Ladder','Issued')");

		echo 'Success';

		mysqli_close($con);
	}
}

// Are we here to withdraw a challenge or remove a match?
if ($_POST['behavior'] == 'removeMatch') {
	global $dbUser, $dbPass, $dbTable;
	$con=mysqli_connect('localhost', $dbUser, $dbPass, $dbTable);

	mysqli_query($con, "DELETE FROM Games Where MatchID = '$_POST[matchID]'");

	echo 'Success';

	mysqli_close($con);
}

?>