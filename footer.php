		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.1.min.js"><\/script>')</script>

		<script src="js/modernizr.min.js"></script>
		<script src="js/respond.min.js"></script>
		<script src="js/main.js"></script>

		<script type="text/javascript">
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

			function signinCallback(authResult) {
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
										data: {
											activeUser: resp.emails[i].value
										},
										success: function(data) {
											var userData = $.parseJSON(data);
											console.log(userData.Email);
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
				}
			}

			render();
		</script>

	</body>
</html>
