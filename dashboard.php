<?php
	
	require_once('config.php');

	if ($_POST['activeUser']) {
		$user = $_POST['activeUser'];
		$userID = '';
		$playerInfo = array();
		$matchInfo = array();
		$history = array();
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

		$historyResult = mysqli_query($con,"SELECT * FROM History WHERE PlayerID = '$userID'");
		while($row = mysqli_fetch_assoc($historyResult)) {
			$history[] = $row;
		}

		$combined = array(
			"playerInfo" => $playerInfo,
			"matchInfo" => $matchInfo,
			"history" => $history
		);
		echo json_encode($combined);
		exit;
	}

?>

<?php include_once('header.php') ?>

<div class="wrapper dashboard">
	<nav class="primaryNav">
		<ul>
			<li><a href="#">My Dashboard</a></li>
			<li><a href="#">League Standings</a></li>
			<li><a href="#">Ladder Standings</a></li>
			<li><a href="#">History</a></li>
		</ul>
	</nav>
	
	<h1>HxPPL - Dashboard</h1>

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
				<td id="enrollment">
				</td>
				<td id="leagueRecord">
				</td>
				<td id="rank">
				</td>
				<td id="matches">
				</td>
			</tr>
		</table>

		<table cellpadding="0" cellspacing="0" class="dataTable ladderTable">
			<tr class="header">
				<td>
					Ladder Record
				</td>
				<td>
					RPI
				</td>
				<td>
					SOS
				</td>
				<td>
					Trend
				</td>
			</tr>
			<tr>
				<td id="ladderRecord">
				</td>
				<td id="rpi">
				</td>
				<td id="sos">
				</td>
				<td id="trend">
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