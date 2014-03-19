		<div class="popupContainer reportMatch">
			<div class="popupContent">
				<b class="closePopup" onclick="$(this).closest('.popupContainer').toggle();">X</b>
				<h3>Report A Match</h3>
				<p class="userScore">
					How many rounds did you win?
					<select class="playerRounds">
						<option value="0">0</option>
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
					</select>
				</p>
				<p class="userScore">
					How many rounds did <span class="opponentName"></span> win?
					<select class="opponentRounds">
						<option value="0">0</option>
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
					</select>
				</p>
				<button type="submit" class="btn submitReport">Submit Report</button>
			</div>
		</div>

		<div class="popupContainer issueChallenge">
			<div class="popupContent">
				<b class="closePopup" onclick="$(this).closest('.popupContainer').toggle();">X</b>
				<h3>Submit a Challenge</h3>
				<p>Are you sure you want to issue a challenge to <span class="opponentName"></span>?</p>
				<button type="submit" class="btn submitChallenge">Yes, I'm going to whip some ass</button>
			</div>
		</div>

		<div class="popupContainer withdrawChallenge">
			<div class="popupContent">
				<b class="closePopup" onclick="$(this).closest('.popupContainer').toggle();">X</b>
				<h3>Withdraw a Challenge</h3>
				<p>Are you sure you want to cancel this challenge?</p>
				<button type="submit" class="btn removeChallenge">Yes, I'm terrified.</button>
			</div>
		</div>

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.1.min.js"><\/script>')</script>

		<script src="js/modernizr.min.js"></script>
		<script src="js/respond.min.js"></script>
		<script src="js/main.js"></script>

	</body>
</html>
