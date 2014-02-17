<?php include_once('header.php') ?>

<div class="wrapper dashboard">
	<h1>Healthx Ping Pong League - Dashboard</h1>
	<nav class="primaryNav">
		<ul>
			<li><a href="#">My Dashboard</a></li>
			<li><a href="#">League Standings</a></li>
			<li><a href="#">Ladder Standings</a></li>
			<li><a href="#">History</a></li>
		</ul>
	</nav>

	<div class="seasonInfo">
		<h3>Season Snapshot</h3>
		<table cellpadding="0" cellspacing="0" class="dataTable leagueTable">
			<tr class="header">
				<td>
					League Enrollment
				</td>
				<td>
					Record
				</td>
				<td>
					Rank
				</td>
				<td>
					Matches Remaining
				</td>
			</tr>
		</table>

		<table cellpadding="0" cellspacing="0" class="dataTable ladderTable">
			<tr class="header">
				<td>
					Record
				</td>
				<td>
					RPI
				</td>
				<td>
					SOS
				</td>
				<td>
					Trend
				</td>
			</tr>
		</table>

		<h3>My Actions</h3>
		<a href="#">Report a Match</a>
		<a href="#">Issue a Ladder Challenge</a>
		<a href="#">Signup For A League</a>
	</div>
</div>

<?php include_once('footer.php') ?>