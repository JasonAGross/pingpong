<?php

	session_start();

	if ($_SESSION['status'] === false) {
		header("Location: index.html");
		die();
	}
	
	include 'functions.php';

	ChromePhp::log($_SESSION);

	$userID = '';
	$playerInfo = array();
	$matchInfo = array();

	if ($_SESSION['user']) {
		$user = $_SESSION['user'];
		$con=mysqli_connect("localhost", $dbUser, $dbPass, $dbTable);

		$playerResult = mysqli_query($con,"SELECT * FROM Players WHERE Players.Email = '$user' LIMIT 1");
		while($row = mysqli_fetch_assoc($playerResult)) {
			$playerInfo = $row;
			$userID = $row['PlayerID'];
		}

		$matchResult = mysqli_query($con,"SELECT * FROM Games WHERE ChallengerID = '$userID' OR DefenderID = '$userID' ORDER BY PlayedOn DESC");
		while($row = mysqli_fetch_assoc($matchResult)) {
			$matchInfo[] = $row;
		}
	}
	
	ChromePhp::log($playerInfo);

?>

<?php include_once('header.php') ?>

<div class="wrapper dashboard">
	
	<h1>My Dashboard</h1>

	<div class="seasonInfo">
		<h3>Season Snapshot</h3>
		<table cellpadding="0" cellspacing="0" class="dataTable leagueTable">
			<tr class="header">
				<td>
					League Enrollment
				</td>
				<td>
					League Record
				</td>
				<td>
					Rank
				</td>
				<td>
					Matches Remaining
				</td>
			</tr>
			<tr>
				<td>
					<?php if($playerInfo['LeagueSeason'] === '') { echo 'Not Enrolled'; } else { echo 'Season '.$playerInfo['LeagueSeason']; } ?>
				</td>
				<td>
					<?php echo getRecord($playerInfo['PlayerID'], $playerInfo['LeagueSeason'], 'League'); ?>
				</td>
				<td>
					<?php echo getRank($playerInfo['PlayerID'], $playerInfo['LeagueSeason'], 'League'); ?>
				</td>
				<td>
					<?php echo getRemainingMatches($playerInfo['PlayerID'], $matchInfo); ?>
				</td>
			</tr>
		</table>

		<h3>Ladder Snapshot</h3>
		<table cellpadding="0" cellspacing="0" class="dataTable ladderTable">
			<tr class="header">
				<td>
					Ladder Record
				</td>
				<td>
					Rank
				</td>
				<td>
					Score
				</td>
				<td>
					RPI
				</td>
				<td>
					Trend
				</td>
			</tr>
			<tr>
				<td>
					<?php echo getRecord($playerInfo['PlayerID'], $playerInfo['LadderSeason'], 'Ladder'); ?>
				</td>
				<td>
					<?php echo getRank($playerInfo['PlayerID'], $playerInfo['LadderSeason'], 'Ladder'); ?>
				</td>
				<td>
					<?php echo $playerInfo['Score']; ?>
				</td>
				<td>
					<?php echo getRPI($playerInfo['PlayerID'], $playerInfo['LadderSeason'], 'Ladder'); ?>
				</td>
				<td>
					<?php echo getTrend($playerInfo['PlayerID'], $playerInfo['LadderSeason'], 'Ladder'); ?>
				</td>
			</tr>
		</table>

		<h3>My Actions</h3>
		<a href="#" class="dashboardAction"><span class="buttonIcon icon-signup"></span>Report a Match</a>
		<a href="#" class="dashboardAction"><span class="buttonIcon icon-hammer"></span>Issue a Ladder Challenge</a>
		<a href="#" class="dashboardAction"><span class="buttonIcon icon-trophy"></span>Signup For A League</a>
	</div>
</div>

<?php include_once('footer.php') ?>