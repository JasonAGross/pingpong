<?php

	session_start();

	if ($_SESSION['status'] === false) {
		header("Location: index.html");
		die();
	}
	
	include 'functions.php';

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

	$_SESSION['Access'] = $playerInfo['Access'];

	ChromePhp::log($matchInfo);

?>

<?php include_once('header.php') ?>

<div class="wrapper dashboard clearfix">

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
		<a href="ladder.php" class="dashboardAction"><span class="buttonIcon icon-hammer"></span>Issue a Ladder Challenge</a>
		<a href="#" class="dashboardAction"><span class="buttonIcon icon-trophy"></span>Signup For A League</a>
	</div>

	<div class="eventFeed">
		<?php
		$ladderMatches = array();
		$leagueMatches = array();
		$con=mysqli_connect("localhost", $dbUser, $dbPass, $dbTable);

		$ladderResult = mysqli_query($con,"SELECT MatchID, ChallengerID, DefenderID, Status FROM Games 
											WHERE (ChallengerID = '$userID' OR DefenderID = '$userID')
											AND MatchType = 'Ladder' 
											AND (Status = 'Pending' OR Status = 'Issued')");
		while($row = mysqli_fetch_assoc($ladderResult)) {
			$ladderMatches[] = $row;
		}

		$leagueResult = mysqli_query($con,"SELECT MatchID, ChallengerID, DefenderID, Status FROM Games 
											WHERE (ChallengerID = '$userID' OR DefenderID = '$userID')
											AND MatchType = 'League' 
											AND Status = 'Pending'");
		while($row = mysqli_fetch_assoc($leagueResult)) {
			$leagueMatches[] = $row;
		}

		if (count($ladderMatches) > 0) {

			?>
			<h3>Challenge Feed</h3>
			<ul class="pendingMatches">
				<?php
				foreach ($ladderMatches as $key => $value) {
					if ($value['Status'] == 'Pending') {
						if ($value['ChallengerID'] == $userID) {
							echo '<li class="alertGreen clearfix">' . getName($value['DefenderID']) . ' has accepted your challenge. <a href="#" onclick="reportMatch(' . $value['MatchID'] . ',' . $value['ChallengerID'] . ',' . $value['DefenderID'] . ',\'' . getName($value['DefenderID']) . '\',\'Ladder\',\'Challenge\')" class="btn">Report</a></li>';
						} else {
							echo '<li class="alertGreen clearfix">You have an accepted challenge from ' . getName($value['ChallengerID']) . '<a href="#" onclick="reportMatch(' . $value['MatchID'] . ',' . $value['DefenderID'] . ',' . $value['ChallengerID'] . ',\'' . getName($value['ChallengerID']) . '\',\'Ladder\',\'Defend\')" class="btn">Report</a></li>';
						}
					} else if ($value['Status'] == 'Issued') {
						if ($value['ChallengerID'] == $userID) {
							echo '<li class="alertYellow clearfix">' . getName($value['DefenderID']) . ' has not yet accepted your challenge.</li>';
						} else {
							echo '<li class="alertRed clearfix">You have been challenged by ' . getName($value['ChallengerID']) . '<a class="btn" onclick="acceptChallenge(' . $value['MatchID'] . ')">Accept</a><a class="btn" onclick="refuseChallenge(' . $value['MatchID'] . ')">Refuse</a></li>';
						}
					} else if ($value['Status'] == 'Refused') {
						echo '<li class="alertRed">' . getName($value['DefenderID']) . ' has refused your challenge. They are a coward. <a class="btn" onclick="confirmRefuse(' . $value['MatchID'] . ')">Well... Okay</a></li>';
					}
				}
				?>
			</ul>
			<?php
		}
		if (count($leagueMatches) > 0) {
			?>
			<h3>League Matchups</h3>
			<ul class="pendingMatches">
				<?php
				foreach ($leagueMatches as $key => $value) {
					if ($value['ChallengerID'] == $userID) {
						echo '<li class="alertGreen clearfix">' . getName($value['DefenderID']) . ' has accepted your challenge.<a href="#" class="btn">Report</a></li>';
					} else {
						echo '<li class="alertGreen clearfix">You have an accepted challenge from ' . getName($value['ChallengerID']) . '<a href="#" class="btn">Report</a></li>';
					}
				}
				?>
			</ul>
			<?php
		}
		if (count($ladderMatches) == 0 && count($leagueMatches) == 0) {
			echo '<p>You have no pending matches. Issue a ladder challenge or signup for the next league season!</p>';
		}
		?>
	</div>

</div>

<?php include_once('footer.php') ?>