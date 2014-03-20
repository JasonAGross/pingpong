<?php
	
	session_start();

	// Make sure the user is logged in
	if ($_SESSION['status'] === false) {
		header("Location: index.html");
		die();
	}
	
	include 'functions.php';

	// Build the results list
	$playerList = array();
	$con=mysqli_connect("localhost", $dbUser, $dbPass, $dbTable);

	$playerResult = mysqli_query($con,"SELECT Players.PlayerID, Players.Name, Players.LadderSeason, Players.Score FROM Players, Globals WHERE Players.LadderSeason = Globals.LadderSeason");
	while($row = mysqli_fetch_assoc($playerResult)) {
		$playerList[] = $row;
	}

	foreach($playerList as $key => $value) {
		$player = $playerList[$key]['PlayerID'];
		$season = $playerList[$key]['LadderSeason'];
		$type = 'Ladder';
		$playerList[$key]['rank'] = getRank($player, $season, $type);
		$playerList[$key]['record'] = getRecord($player, $season, $type);
		$playerList[$key]['rpi'] = getRPI($player, $season, $type);
		$playerList[$key]['trend'] = getTrend($player, $season, $type);
	}

	foreach ($playerList as $key => $row) {
		$rank[$key] = $row['rank'];
	}

	array_multisort($rank, SORT_ASC, $playerList);
?>


<?php include_once('header.php') ?>

<div class="wrapper ladderStandings">

	<h1>Current Ladder Standings</h1>

	<div class="ladder">
		<table cellpadding="0" cellspacing="0" class="dataTable currentStandings">
			<tr class="header">
				<td>Rank</td>
				<td>Player</td>
				<td>Record</td>
				<td>RPI</td>
				<td>Score</td>
				<td>Trend</td>
				<td>Actions</td>
			</tr>
			<?php
				foreach($playerList as $key => $value) {
					echo '<tr><td>' . $value['rank'] . '</td><td>' . $value['Name'] . '</td><td>' . $value['record'] . '</td><td>' . $value['rpi']  . '</td><td>' . $value['Score'] . '</td><td>' . $value['trend'] . '</td><td>' . getActions($_SESSION['user'], $value['PlayerID'], 'Ladder') . '</td></tr>';
				}
			?>
		</table>
	</div>

</div>

<?php include_once('footer.php') ?>