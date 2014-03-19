function reportMatch(matchID, player, opponent, opponentName, matchType, matchRole) {
	$('.reportMatch .opponentName').html(opponentName.substring(0, opponentName.indexOf(' ')));
	$('.reportMatch .submitReport').attr('onclick','submitReport(' + matchID + ',' + player + ',' + opponent + ',\'' + matchType + '\',\'' + matchRole + '\')');
	$('.reportMatch').show();
}

function submitReport(matchID, player, opponent, matchType, matchRole) {
	var playerScore = parseInt($('.playerRounds option:selected').val());
	var opponentScore = parseInt($('.opponentRounds option:selected').val());
	$.ajax({
		type: 'post',
		url: 'report.php',
		data: {
			behavior: 'submitScore',
			matchID: matchID,
			player: player,
			playerScore: playerScore,
			opponent: opponent,
			opponentScore: opponentScore,
			matchRole: matchRole
		},
		success: function(result) {
			if (result == 'Success') {
				$('.reportMatch .popupContent').html('<h3>Match Reported</h3><p>Thanks for the report, your matches have been updated.</p>');
				setTimeout(function() {
					location.reload();
				}, 2000);
			} else {
				$('.reportMatch .popupContent h3').after('<p class="alertRed">' + result + '</p>');
			}
		}
	});
}

function issueChallenge(player, opponent, opponentName, matchType) {
	$('.issueChallenge .opponentName').html(opponentName.substring(0, opponentName.indexOf(' ')));
	$('.issueChallenge .submitChallenge').attr('onclick','submitChallenge(' + player + ',' + opponent + ',\'' + matchType + '\')');
	$('.issueChallenge').show();
}

function submitChallenge(player, opponent, matchType) {
	$.ajax({
		type: 'post',
		url: 'report.php',
		data: {
			behavior: 'issueChallenge',
			player: player,
			opponent: opponent,
			matchType: matchType
		},
		success: function(result) {
			if (result == 'Success') {
				$('.issueChallenge .popupContent').html('<h3>Challenge Issued</h3><p>Your opponent will need to accept the challenge before this match can be reported.</p>');
				setTimeout(function() {
					location.reload();
				}, 3000);
			} else {
				$('.issueChallenge .popupContent h3').after('<p class="alertRed">' + result + '</p>');
			}
		}
	});
}

function withdrawChallenge(matchID) {
	$('.withdrawChallenge .removeChallenge').attr('onclick','removeMatch(' + matchID + ')');
	$('.withdrawChallenge').show();
}

function removeMatch(matchID) {
	$.ajax({
		type: 'post',
		url: 'report.php',
		data: {
			behavior: 'removeMatch',
			matchID: matchID
		},
		success: function(result) {
			if (result == 'Success') {
				$('.withdrawChallenge .popupContent').html('<h3>Challenge Removed</h3><p>You are no longer tied to your former convictions.</p>');
				setTimeout(function() {
					location.reload();
				}, 2000);
			} else {
				$('.withdrawChallenge .popupContent h3').after('<p class="alertRed">' + result + '</p>');
			}
		}
	});
}