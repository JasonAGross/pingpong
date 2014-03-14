var userData = {};

(function() {
	var po = document.createElement('script'); 
	po.type = 'text/javascript'; 
	po.async = true;
	po.src = 'https://apis.google.com/js/client:plusone.js';
	var s = document.getElementsByTagName('script')[0]; 
	s.parentNode.insertBefore(po, s);
})();

/*
function render() {

	// Additional params including the callback, the rest of the params will
	// come from the page-level configuration.
	var additionalParams = {
		'callback': signinCallback
	};

	// Attach a click listener to a button to trigger the flow.
	var signinButton = document.getElementById('googleAuth');
	signinButton.addEventListener('click', function() {
		gapi.auth.signIn(additionalParams); // Will use page level configuration
	});
}
*/

var callbackRun = true;

function signinCallback(authResult) {

	if(callbackRun) {
		callbackRun = false;
		if (authResult['status']['signed_in'] && $('.auth').length > 0) {
			// Update the app to reflect a signed in user
			// Hide the sign-in button now that the user is authorized, for example:
			$('.auth').hide();
			gapi.client.load('plus','v1', function(){
				var request = gapi.client.plus.people.get({
					'userId': 'me'
				});
				request.execute(function(resp) {
					for(i=0;i<resp.emails.length;i++) {
						if (resp.emails[i].type == 'account') {
							// Check the email returned from authorization against our database for a match
							for(j=0;j<emails.length;j++) {
								if (emails[j] == resp.emails[i].value) {
									// We have an account for this user
									window.location = 'dashboard.php';
									return;
								}
							}
							// No Email match, we can show the registration now. 
							$('.register').show();
							$('#regEmail').attr('value', resp.emails[i].value);
						}
					}
					$('#regName').attr('value', resp.displayName);
					$('#userImg').attr('src',resp.image.url);
					$('#fName').html(resp.name.givenName);
				});
			});
		} else if (authResult['status']['signed_in']) {
			if ($('.auth').length > 0) {
				window.location('dashboard.php');
			}
			// Logged into a sub-page
			gapi.client.load('plus','v1', function(){
				var request = gapi.client.plus.people.get({
					'userId': 'me'
				});
				request.execute(function(resp) {
					for(i=0;i<resp.emails.length;i++) {
						if (resp.emails[i].type == 'account') {
							$.ajax({
								type: 'post',
								url: 'dashboard.php',
								data: {
									activeUser: resp.emails[i].value
								},
								success: function(data) {
									// userData = $.parseJSON(data);
									if ($('.dashboard').length > 0) {
										// buildDashboard();
									}
									return;
								}
							});
						}
					}
				});
			});
		} else {
			// Update the app to reflect a signed out user
			// Possible error values:
			//   "user_signed_out" - User is signed-out
			//   "access_denied" - User denied access to your app
			//   "immediate_failed" - Could not automatically log in the user
			console.log('Sign-in state: ' + authResult['error']);
			window.location = 'index.php';
		}
	}
}

function buildRecord(Player, Type, Matches) {
	var wins = 0;
	var losses = 0;
	for (var i = 0; i < Matches.length; i++) {
		if(Matches[i].MatchType == Type && Matches[i].Status == 'Complete') {
			if (Matches[i].ChallengerID == Player && Matches[i].ChallengerScore > Matches[i].DefenderScore || Matches[i].DefenderID == Player && Matches[i].ChallengerScore < Matches[i].DefenderScore) {
				wins++;
			} else {
				losses++;
			}
		}
	}
	return wins + '-' + losses;
}

function getRPIValue(callback, Player, Season) {
	$.ajax({
		type: 'post',
		url: 'ladder.php',
		data: {
			getRPIUser: Player,
			season: Season
		},
		success: function(data) {
			callback(data);
		}
	});
}

function getTrend(Player, Type, Matches) {
	var trend = '';
	var trendCount = 0;
	for (var i = 0; i < Matches.length; i++) {
		if (trend.length > 0) {
			if(Matches[i].MatchType == Type && Matches[i].Status == 'Complete') {
				var tempTrend = '';
				if (Matches[i].ChallengerID == Player && Matches[i].ChallengerScore > Matches[i].DefenderScore || Matches[i].DefenderID == Player && Matches[i].ChallengerScore < Matches[i].DefenderScore) {
					tempTrend = 'Won ';
				} else {
					tempTrend = 'Lost ';
				}
				if (tempTrend === trend) {
					trendCount++;
				} else {
					break;
				}
			}
		} else {
			if(Matches[i].MatchType == Type && Matches[i].Status == 'Complete') {
				if (Matches[i].ChallengerID == Player && Matches[i].ChallengerScore > Matches[i].DefenderScore || Matches[i].DefenderID == Player && Matches[i].ChallengerScore < Matches[i].DefenderScore) {
					trend = 'Won ';
					trendCount++;
				} else {
					trend = 'Lost ';
					trendCount++;
				}
			}
		}
	}
	return trend + ' ' + trendCount;
}

function buildDashboard(rpiResp) {
	var PID = userData.playerInfo.PlayerID;
	// League Dashboard
	if (userData.LeagueSeason) {
		$('#enrollment').html('Season' + userData.LeagueSeason);
	} else {
		$('#enrollment').html('N/A');
	}

	var leaguePending = 0;
	for (var i = 0; i < userData.matchInfo.length; i++) {
		if(userData.matchInfo[i].MatchType == 'League' && userData.matchInfo[i].Status == 'Pending') {
			leaguePending++;
		}
	}
	var leagueRecord = buildRecord(PID, 'League', userData.matchInfo);
	var ladderRecord = buildRecord(PID, 'Ladder', userData.matchInfo);

	$('#leagueRecord').html(leagueRecord);
	$('#matches').html(leaguePending);

	// Ladder Dashboard
	$('#ladderRecord').html(ladderRecord);
	if (rpiResp) {
		$('#rpi').html(rpiResp);
	} else {
		getRPIValue(buildDashboard, PID, userData.LeagueSeason);
	}

	var trend = getTrend(PID, 'Ladder', userData.matchInfo);
	$('#trend').html(trend);

}

function buildLadderStandings() {
	var ladder = {};
	$.ajax({
		type: 'post',
		url: 'ladder.php',
		data: {
			action: 'buildLadder',
			Season: 0
		},
		success: function(data) {
			ladder = $.parseJSON(data);
			buildLadder(ladder);
		}
	});
}

function buildLadder(matchData) {
	/*
	var players = {
		name: [],
		record: [],
		rpi: [],
		trend: []
	};
	for (var i = 0; i < matchData.players.length; i++) {
		players.name.push(matchData.players[i].Name);
		players.record.push(buildRecord(matchData.players[i].PlayerID, "Ladder", matchData.matches));
		players.rpi.push('RPI');
		players.trend.push(getTrend(matchData.players[i].PlayerID, "Ladder", matchData.matches));
	};
	*/
	console.log(matchData);
	for (var i = 0; i < matchData.players.length; i++) {
		var rank = i+1;
		matches = [];
		for (var j = 0; j < matchData.matches.length; j++) {
			if (matchData.players[i].PlayerID == matchData.matches[j].ChallengerID || matchData.players[i].PlayerID == matchData.matches[j].DefenderID) {
				matches.push(matchData.matches[j]);
			}
		};
		console.log(matches);
		row = '<tr><td>' + rank + '</td><td>' + matchData.players[i].Name + '</td><td>' + buildRecord(matchData.players[i].PlayerID, "Ladder", matches) + '</td><td> RPI </td><td>' + getTrend(matchData.players[i].PlayerID, "Ladder", matches) + '</td></tr>';
		$('.currentStandings').append(row);
	};
}