<?php
	
	require_once('config.php');

	if ($_POST['getRPIUser']) {
		$user = $_POST['getRPIUser'];
		$season = $_POST['Season'];
		$matches = array();
		$con=mysqli_connect("localhost", $dbUser, $dbPass, $dbTable);

		$matchResult = mysqli_query($con,"SELECT * FROM Games WHERE SeasonID = '$season' AND MatchType = 'Ladder' AND Status = 'Complete'");
		while($row = mysqli_fetch_assoc($matchResult)) {
			$matches[] = $row;
		}

		function getWinRatio($player, $matches) {
			$matchesPlayed = 0;
			$matchesWon = 0;
			foreach ($matches as $key => $value) {
				if ($value['ChallengerID'] == $player || $value['DefenderID'] == $player) {
					$matchesPlayed++;
					if ($value['ChallengerID'] == $player && $value['ChallengerScore'] > $value['DefenderScore'] || $value['DefenderID'] == $player && $value['ChallengerScore'] < $value['DefenderScore']) {
						$matchesWon++;
					}
				}
			}
			return $matchesWon / $matchesPlayed;
		}

		function getOpponents($player, $matches) {
			$opponents = array();
			foreach ($matches as $key => $value) {
				if ($value['ChallengerID'] == $player) {
					if(!in_array($value['DefenderID'], $opponents)) {
						$opponents[] = $value['DefenderID'];
					}
				} else if ($value['DefenderID'] == $player){
					if(!in_array($value['ChallengerID'], $opponents)) {
						$opponents[] = $value['ChallengerID'];
					}
				}
			}
			return $opponents;
		}

		function array_flatten($array) { 
			if (!is_array($array)) { 
				return FALSE; 
			} 
			$result = array(); 
			foreach ($array as $key => $value) { 
				if (is_array($value)) { 
					$result = array_merge($result, array_flatten($value)); 
				} 
				else { 
					$result[$key] = $value; 
				} 
			} 
			return $result; 
		} 

		// First tier opponents, people played directly
		$opponents = getOpponents($user, $matches);
		$opponentsRatio = array();
		foreach ($opponents as $key => $value) {
			$opponentsRatio[] = getWinRatio($value, $matches);
		}

		// Second tier opponents, people played by first tier opponents
		$opponentsOfOpponents = array();
		foreach ($opponents as $opp => $oppID) {
			$opponentsOfOpponents[] = getOpponents($oppID, $matches);
		}
		$opponentsOfOpponents = array_flatten($opponentsOfOpponents);
		$opponentsOfOpponentsRatio = array();
		foreach ($opponentsOfOpponents as $key => $value) {
			$opponentsOfOpponentsRatio[] = getWinRatio($value, $matches);
		}

		$playerRatio = getWinRatio($user, $matches);
		$opponentsRatio = array_sum($opponentsRatio) / count($opponentsRatio);
		$opponentsOfOpponentsRatio = array_sum($opponentsOfOpponentsRatio) / count($opponentsOfOpponentsRatio);

		$rpi = ($playerRatio + $opponentsRatio + $opponentsOfOpponentsRatio) / 3;

		echo round($rpi, 4);
		exit;
	}

?>

<?php include_once('header.php') ?>

<div class="wrapper ladderStandings">

</div>

<?php include_once('footer.php') ?>