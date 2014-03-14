<?php
	
	session_start();

	if ($_SESSION['status'] === false) {
		header("Location: index.html");
		die();
	}
	
	include 'functions.php';

	$playerList = array();
	$con=mysqli_connect("localhost", $dbUser, $dbPass, $dbTable);

	$playerResult = mysqli_query($con,"SELECT Players.PlayerID, Players.Name, Players.LadderSeason FROM Players, Globals WHERE Players.LadderSeason = Globals.LadderSeason");
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

	ChromePhp::log($playerList);
?>


<?php include_once('header.php') ?>

<div class="wrapper ladderStandings">

	<h1>Current Ladder Standings</h1>

	<div class="ladder">
		<table cellpadding="0" cellspacing="0" class="dataTable currentStandings">
			<tr>
				<th>Rank</th>
				<th>Player</th>
				<th>Record</th>
				<th>RPI</th>
				<th>Trend</th>
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