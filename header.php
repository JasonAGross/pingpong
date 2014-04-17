<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" itemscope itemtype="http://schema.org/Article"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" itemscope itemtype="http://schema.org/Article"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" itemscope itemtype="http://schema.org/Article"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" itemscope itemtype="http://schema.org/Article"> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title></title>
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width">
		<meta name="google-signin-clientid" content="93325758235-6q0k0rmvr972lnnl5fgorrk85vjpngrj.apps.googleusercontent.com" />
		<meta name="google-signin-scope" content="https://www.googleapis.com/auth/plus.login email" />
		<!-- <meta name="google-signin-requestvisibleactions" content="http://schemas.google.com/AddActivity" /> -->
		<meta name="google-signin-cookiepolicy" content="single_host_origin" />
		<meta name="google-signin-callback" content="signInCallback" />

		<link rel="stylesheet" href="css/normalize.min.css">
		<link rel="stylesheet" href="css/main.css">
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,700' rel='stylesheet prefetch' type='text/css'>
		<script src="js/prefixfree.min.js"></script>

		<!--[if lt IE 9]>
			<script src="js/vendor/html5-3.6-respond-1.1.0.min.js"></script>
		<![endif]-->
	</head>
	<body>
	<!--[if lt IE 7]>
		<p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
	<![endif]-->


	<span class="logout clearfix">
		<a href="index.html" class="logoutButton">Logout</a>
	</span>
	<nav class="primaryNav clearfix">
		<span id="mainLogo"><img src="img/logo.png"></span>
		<ul>
			<li><a href="dashboard.php">My Dashboard</a></li><li><a href="league.php">League Standings</a></li><li><a href="ladder.php">Ladder Standings</a></li><li><a href="history.php">History</a></li><?php if($_SESSION['Access'] == 2) { ?><li><a href="admin.php">Admin</a></li><?php } ?>
		</ul>
	</nav>