<?php

include 'config.php';
include 'ChromePhp.php';

// General purpose function to make simplifying arrays easier.
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

// Get the win/loss ratio for a given player. $matches should be an array of all matches this player has participated in.
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

// Grab just the pure win and loss record for a given player, season, and season type.
function getRecord($player, $season, $type) {
	global $dbUser, $dbPass, $dbTable;
	$matches = array();
	$con=mysqli_connect('localhost', $dbUser, $dbPass, $dbTable);

	$matchResult = mysqli_query($con,"SELECT * FROM Games WHERE SeasonID = '$season' AND MatchType = '$type' AND Status = 'Complete' AND (ChallengerID = '$player' OR DefenderID = '$player')");
	while($row = mysqli_fetch_assoc($matchResult)) {
		$matches[] = $row;
	}

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
	return $matchesWon . '-' . ($matchesPlayed - $matchesWon);
}

// Calculate how many matches this player has left in the League season. Matches should be an array.
function getRemainingMatches($player, $matches) {
	$remaining = 0;
	foreach ($matches as $key => $value) {
		if ($value['Status'] != 'Complete') {
			$remaining++;
		}
	}
	return $remaining;
}

// Get opponents of a given player. Matches should be an array. 
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

// Build an RPI calculation for a player in a given season. 
function getRPI($player, $season, $type) {
	global $dbUser, $dbPass, $dbTable;
	$matches = array();
	$con=mysqli_connect('localhost', $dbUser, $dbPass, $dbTable);

	$matchResult = mysqli_query($con,"SELECT * FROM Games WHERE SeasonID = '$season' AND MatchType = '$type' AND Status = 'Complete'");
	while($row = mysqli_fetch_assoc($matchResult)) {
		$matches[] = $row;
	}

	// First tier opponents, people played directly
	$opponents = getOpponents($player, $matches);
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

	// Get win percentages
	$playerRatio = getWinRatio($player, $matches);
	$opponentsRatio = array_sum($opponentsRatio) / count($opponentsRatio);
	$opponentsOfOpponentsRatio = array_sum($opponentsOfOpponentsRatio) / count($opponentsOfOpponentsRatio);

	// Weights for RPI value, must add up to 1
	$rpi = $playerRatio * .25;
	$rpi = $rpi + ($opponentsRatio * .5);
	$rpi = $rpi + ($opponentsOfOpponentsRatio * .25);

	return round($rpi, 4);

	mysqli_close($con);
}

// Calculate the latest trend for a user, current winning or losing streak
function getTrend($player, $season, $type) {
	global $dbUser, $dbPass, $dbTable;
	$matches = array();
	$con=mysqli_connect('localhost', $dbUser, $dbPass, $dbTable);

	$matchResult = mysqli_query($con,"SELECT * FROM Games WHERE SeasonID = '$season' AND MatchType = '$type' AND Status = 'Complete' AND (ChallengerID = '$player' OR DefenderID = '$player')");
	while($row = mysqli_fetch_assoc($matchResult)) {
		$matches[] = $row;
	}

	$trend = '';
	$trendCount = 0;
	foreach ($matches as $key => $value) {
		if ($trendCount > 0) {
			$tempTrend = '';
			if ($value['ChallengerID'] == $player && $value['ChallengerScore'] > $value['DefenderScore'] || $value['DefenderID'] == $player && $value['ChallengerScore'] < $value['DefenderScore']) {
				$tempTrend = 'Won';
			} else {
				$tempTrend = 'Lost';
			}
			if ($tempTrend == $trend) {
				$trendCount++;
			} else {
				break;
			}
		} else {
			if ($value['ChallengerID'] == $player && $value['ChallengerScore'] > $value['DefenderScore'] || $value['DefenderID'] == $player && $value['ChallengerScore'] < $value['DefenderScore']) {
				$trend = 'Won';
				$trendCount++;
			} else {
				$trend = 'Lost';
				$trendCount++;
			}
		}
	}

	return $trend . ' ' . $trendCount;
}

// Find a players rank for a given season and season type
function getRank($player, $season, $type) {
	if ($type == 'Ladder') { // We rank ladder standings by RPI
		global $dbUser, $dbPass, $dbTable;
		$players = array();
		$con=mysqli_connect('localhost', $dbUser, $dbPass, $dbTable);

		$playerResult = mysqli_query($con,"SELECT PlayerID FROM Players WHERE LadderSeason = '$season'");
		while($row = mysqli_fetch_assoc($playerResult)) {
			$players[] = $row;
		}

		foreach($players as $key => $value) {
			$players[$key]['rpi'] = getRPI($players[$key]['PlayerID'], $season, $type);
		}

		foreach ($players as $key => $row) {
			$rpi[$key] = $row['rpi'];
		}

		array_multisort($rpi, SORT_DESC, $players);
		
		foreach ($players as $key => $value) {
			if ($players[$key]['PlayerID'] === $player) {
				return $key+1;
			}
		}

	} else { // We rank league standings by least number of losses with an RPI tie-breaker
		global $dbUser, $dbPass, $dbTable;
		$players = array();
		$con=mysqli_connect('localhost', $dbUser, $dbPass, $dbTable);

		$playerResult = mysqli_query($con,"SELECT PlayerID FROM Players WHERE LadderSeason = '$season'");
		while($row = mysqli_fetch_assoc($playerResult)) {
			$players[] = $row;
		}

		foreach($players as $key => $value) {
			$players[$key]['losses'] = substr(getRecord($players[$key]['PlayerID'], $season, $type), -1);
		}

		foreach ($players as $key => $row) {
			$losses[$key] = $row['losses'];
		}

		array_multisort($losses, SORT_ASC, $players);
		
		foreach ($players as $key => $value) {
			if ($players[$key]['PlayerID'] === $player) {
				return $key+1;
			}
		}
	}
}

?>