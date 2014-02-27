<?php include_once('header.php') ?>

<div class="wrapper dashboard">
	<nav class="primaryNav">
		<ul>
			<li><a href="#">My Dashboard</a></li>
			<li><a href="#">League Standings</a></li>
			<li><a href="#">Ladder Standings</a></li>
			<li><a href="#">History</a></li>
		</ul>
	</nav>
	
	<h1>HxPPL - Dashboard</h1>

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
			<tr>
				<td>
					Season 1
				</td>
				<td>
					14-4
				</td>
				<td>
					3
				</td>
				<td>
					2
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
			<tr>
				<td>
					18-8
				</td>
				<td>
					.6331 (5)
				</td>
				<td>
					.4401 (12)
				</td>
				<td>
					Lost 2
				</td>
			</tr>
		</table>

		<h3>My Actions</h3>
		<a href="#" class="dashboardAction"><span class="buttonIcon icon-signup"></span>Report a Match</a>
		<a href="#" class="dashboardAction"><span class="buttonIcon icon-hammer"></span>Issue a Ladder Challenge</a>
		<a href="#" class="dashboardAction"><span class="buttonIcon icon-trophy"></span>Signup For A League</a>
	</div>
</div>

<?php include_once('footer.php') ?>