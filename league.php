<?php
	
	session_start();

	if ($_SESSION['status'] === false) {
		header("Location: index.html");
		die();
	}
	
	include 'functions.php';

	$playerList = array();
	$con=mysqli_connect("localhost", $dbUser, $dbPass, $dbTable);

	$playerResult = mysqli_query($con,"SELECT Players.PlayerID, Players.Name, Players.LeagueSeason, Players.Score FROM Players, Globals WHERE Players.LeagueSeason = Globals.LeagueSeason");
	while($row = mysqli_fetch_assoc($playerResult)) {
		$playerList[] = $row;
	}

	foreach($playerList as $key => $value) {
		$player = $playerList[$key]['PlayerID'];
		$season = $playerList[$key]['LeagueSeason'];
		$type = 'League';
		$playerList[$key]['rank'] = getRank($player, $season, $type);
		$playerList[$key]['record'] = getRecord($player, $season, $type);
		$playerList[$key]['rpi'] = getRPI($player, $season, $type);
		$playerList[$key]['trend'] = getTrend($player, $season, $type);
	}

	foreach ($playerList as $key => $row) {
		$rank[$key] = $row['rank'];
	}

	array_multisort($rank, SORT_ASC, $playerList);

	ChromePhp::log($playerList);
?>


<?php include_once('header.php') ?>

<div class="wrapper leagueStandings">

	<h1>Current League Standings</h1>

	<div class="league">
		<table cellpadding="0" cellspacing="0" class="dataTable currentStandings">
			<tr class="header">
				<td>Rank</td>
				<td>Player</td>
				<td>Record</td>
				<td>RPI</td>
				<td>Trend</td>
			</tr>
			<?php
				foreach($playerList as $key => $value) {
					echo '<tr><td>' . $playerList[$key]['rank'] . '</td><td>' . $playerList[$key]['Name'] . '</td><td>' . $playerList[$key]['record'] . '</td><td>' . $playerList[$key]['rpi']  . '</td><td>' . $playerList[$key]['trend'] . '</td></tr>';
				}
			?>
		</table>
	</div>

</div>

<?php include_once('footer.php') ?>