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

		// Check if we are reporting an unchallenged match
		if ($_POST['matchID'] == 'new') {

			$globalResult = mysqli_query($con,"SELECT LadderSeason FROM Globals");
			$grow = mysqli_fetch_assoc($globalResult);
			$season = $grow['LadderSeason'];

			mysqli_query($con, "INSERT INTO Games (SeasonID, ChallengerID, DefenderID, ChallengerScore, DefenderScore, MatchType, Status, PlayedOn) VALUES ('$season', '$challengerID','$defenderID','$challengerScore','$defenderScore','Ladder','Complete','$playedOn')");
			
			echo 'Created';
		// Otherwise its a challenged match
		} else {
			$matchID = $_POST['matchID'];

			mysqli_query($con, "UPDATE Games SET ChallengerScore = '$challengerScore', DefenderScore = '$defenderScore', Status = 'Complete', PlayedOn = '$playedOn' WHERE MatchID = '$matchID'");

			echo 'Success';
		}

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
if ($_POST['behavior'] == 'removeMatch' || $_POST['behavior'] == 'confirmRefuse') {
	global $dbUser, $dbPass, $dbTable;
	$con=mysqli_connect('localhost', $dbUser, $dbPass, $dbTable);

	mysqli_query($con, "DELETE FROM Games Where MatchID = '$_POST[matchID]'");

	echo 'Success';

	mysqli_close($con);
}

// Are we here to refuse or accept a challenge? 
if ($_POST['behavior'] == 'acceptChallenge' || $_POST['behavior'] == 'refuseChallenge') {
	global $dbUser, $dbPass, $dbTable;
	$con=mysqli_connect('localhost', $dbUser, $dbPass, $dbTable);

	if($_POST['behavior'] == 'acceptChallenge') {
		$status = 'Pending';
	} else {
		$status = 'Refused';
	}

	mysqli_query($con,"UPDATE Games Set Status = '$status' WHERE MatchID = '$_POST[matchID]'");

	echo 'Success';

	mysqli_close($con);

}

?>