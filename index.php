<!DOCTYPE html>
<html lang="en-US">
<head>
	<title>Portal Asset Manager</title>

	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
</head>
<body>
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Yantramanav&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Oxygen+Mono&display=swap" rel="stylesheet">
<link href="style.css?ts=<?php echo time() ?>" rel="stylesheet">

<h1>Portal Asset Manager</h1>

<?php
require_once 'config.php';
require_once 'src.php';

$code = $_GET['code'] ?? null;
$state = $_GET['state'] ?? null;
$bearerToken = $_GET['bearer'] ?? null;

$authToken = AUTH_TOKEN;
$userId = USER_ID;
$apiKey = V3_API_KEY;

$mode = ($code || $bearerToken) ? 'app' : 'login';
switch ($mode) {
case 'app':
	require_once 'view-app.php';
	break;
case 'login':
	require_once 'view-login.php';
	break;
}

?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.6/handlebars.min.js"></script>

</body>
</html>
