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
		<h3>Recent Ladder Matches</h3>
		<table cellpadding="0" cellspacing="0" class="dataTable">
			<tr class="header">
				<td>Match Date</td>
				<td>Challenger / Score</td>
				<td>Defender / Score</td>
				<td>Admin Actions</td>
			</tr>
			<?php 

			$ladderRecent = mysqli_query($con,"SELECT * FROM Games WHERE MatchType = 'Ladder' AND SeasonID = '$thisLadderSeason' AND Status = 'Complete' ORDER BY PlayedOn DESC LIMIT 10");
			while($row = mysqli_fetch_assoc($ladderRecent)) {
				?>
				<tr>
					<td><?php echo $row['PlayedOn']; ?></td>
					<?php if ($row['ChallengerScore'] > $row['DefenderScore']) { ?>
						<td class="winner"><?php echo getName($row['ChallengerID']) . ' / ' . $row['ChallengerScore']; ?></td>
						<td><?php echo getName($row['DefenderID']) . ' / ' . $row['DefenderScore']; ?></td>
					<?php } else { ?>
						<td><?php echo getName($row['ChallengerID']) . ' / ' . $row['ChallengerScore']; ?></td>
						<td class="winner"><?php echo getName($row['DefenderID']) . ' / ' . $row['DefenderScore']; ?></td>
					<?php } ?>
					<td><button class="btn">Reset Match</button><button class="btn">Remove Match</button></td>
				</tr>
				<?php
			}
			?>
		</table>

		<h3>Recent League Matches</h3>
		<table cellpadding="0" cellspacing="0" class="dataTable">
			<tr class="header">
				<td>Match Date</td>
				<td>Challenger / Score</td>
				<td>Defender / Score</td>
				<td>Admin Actions</td>
			</tr>
			<?php 

			$leagueRecent = mysqli_query($con,"SELECT * FROM Games WHERE MatchType = 'League' AND SeasonID = '$thisLeagueSeason' AND Status = 'Complete' ORDER BY PlayedOn DESC LIMIT 10");
			while($row = mysqli_fetch_assoc($leagueRecent)) {
				?>
				<tr>
					<td><?php echo $row['PlayedOn']; ?></td>
					<?php if ($row['ChallengerScore'] > $row['DefenderScore']) { ?>
						<td class="winner"><?php echo getName($row['ChallengerID']) . ' / ' . $row['ChallengerScore']; ?></td>
						<td><?php echo getName($row['DefenderID']) . ' / ' . $row['DefenderScore']; ?></td>
					<?php } else { ?>
						<td><?php echo getName($row['ChallengerID']) . ' / ' . $row['ChallengerScore']; ?></td>
						<td class="winner"><?php echo getName($row['DefenderID']) . ' / ' . $row['DefenderScore']; ?></td>
					<?php } ?>
					<td><button class="btn">Reset Match</button></td>
				</tr>
				<?php
			}
			?>
		</table>
	</div>

</div>

<?php include_once('footer.php') ?>