var userData = {};

(function() {
	var po = document.createElement('script'); 
	po.type = 'text/javascript'; 
	po.async = true;
	po.src = 'https://apis.google.com/js/client:plusone.js';
	var s = document.getElementsByTagName('script')[0]; 
	s.parentNode.insertBefore(po, s);
})();

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
									userData = $.parseJSON(data);
									if ($('.dashboard').length > 0) {
										buildDashboard();
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

function buildDashboard() {
	var PID = userData.playerInfo.PlayerID;
	// League Dashboard
	if (userData.LeagueSeason) {
		$('#enrollment').html('Season' + userData.LeagueSeason);
	} else {
		$('#enrollment').html('N/A');
	}

	var leagueWins = 0;
	var leagueLosses = 0;
	var leaguePending = 0;
	var ladderWins = 0;
	var ladderLosses = 0;
	for (var i = 0; i < userData.matchInfo.length; i++) {
		if(userData.matchInfo[i].MatchType == 'League' && userData.matchInfo[i].Status == 'Complete') {
			if (userData.matchInfo[i].ChallengerID == PID && userData.matchInfo[i].ChallengerScore > userData.matchInfo[i].DefenderScore || userData.matchInfo[i].DefenderID == PID && userData.matchInfo[i].ChallengerScore < userData.matchInfo[i].DefenderScore) {
				leagueWins++;
			} else {
				leagueLosses++;
			}
		}
		if(userData.matchInfo[i].MatchType == 'Ladder' && userData.matchInfo[i].Status == 'Complete') {
			if (userData.matchInfo[i].ChallengerID == PID && userData.matchInfo[i].ChallengerScore > userData.matchInfo[i].DefenderScore || userData.matchInfo[i].DefenderID == PID && userData.matchInfo[i].ChallengerScore < userData.matchInfo[i].DefenderScore) {
				ladderWins++;
			} else {
				ladderLosses++;
			}
		}
		if(userData.matchInfo[i].MatchType == 'League' && userData.matchInfo[i].Status == 'Pending') {
			leaguePending++;
		}
	}

	$('#leagueRecord').html(leagueWins + '-' + leagueLosses);
	$('#matches').html(leaguePending);

	// Ladder Dashboard
	$('#ladderRecord').html(ladderWins + '-' + ladderLosses);

	$.ajax({
		type: 'post',
		url: 'ladder.php',
		data: {
			getRPIUser: PID,
			season: userData.playerInfo.LadderSeason
		},
		success: function(data) {
			$('#rpi').html(data);
		}
	});

	var trend = '';
	var trendCount = 0;
	for (var i = 0; i < userData.matchInfo.length; i++) {
		if (trend.length > 0) {
			if(userData.matchInfo[i].MatchType == 'Ladder' && userData.matchInfo[i].Status == 'Complete') {
				var tempTrend = '';
				if (userData.matchInfo[i].ChallengerID == PID && userData.matchInfo[i].ChallengerScore > userData.matchInfo[i].DefenderScore || userData.matchInfo[i].DefenderID == PID && userData.matchInfo[i].ChallengerScore < userData.matchInfo[i].DefenderScore) {
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
			if(userData.matchInfo[i].MatchType == 'Ladder' && userData.matchInfo[i].Status == 'Complete') {
				if (userData.matchInfo[i].ChallengerID == PID && userData.matchInfo[i].ChallengerScore > userData.matchInfo[i].DefenderScore || userData.matchInfo[i].DefenderID == PID && userData.matchInfo[i].ChallengerScore < userData.matchInfo[i].DefenderScore) {
					trend = 'Won ';
					trendCount++;
				} else {
					trend = 'Lost ';
					trendCount++;
				}
			}
		}
	}
	$('#trend').html(trend + trendCount);

}

function calcRPI(Player, Season) {
	$.ajax({
		type: 'post',
		url: 'ladder.php',
		data: {
			getRPIUser: Player,
			season: Season
		},
		success: function(data) {
			return data;
		}
	});
}

render();