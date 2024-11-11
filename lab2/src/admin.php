<?php 
// Cookies
if (isset($_COOKIE['auth_token_id']) && isset($_COOKIE['auth_token_pass_hash'])) {
	$auth_token_id = $_COOKIE['auth_token_id'];
	$auth_token_pass_hash = $_COOKIE['auth_token_pass_hash'];

	$connect_data = "host=localhost port=5432 dbname=php_auth user=postgres password=postgres";
	$db_connect = pg_connect($connect_data);
	if (!$db_connect) {
		echo json_encode(['error' => 'Database connection error: ' . pg_last_error($db_connect)]);
		exit;
	}
	$query = "SELECT * FROM users WHERE id = '$auth_token_id' limit 1";
	$result = pg_query($db_connect, $query);

	if (!$result) {
		header("Location: index.php");
		exit;
	}

	if (pg_num_rows($result) > 0) {
		$user = pg_fetch_assoc($result);

		if ($auth_token_pass_hash != $user['password_hash']) {
			header('Location: index.php');
			exit;
		}	

		echo "<div>" . "Hello, " . $user['username'] . "!" . "</div>";
	}
	else {
		header('Location: index.php');
		exit;
	}

	pg_close($db_connect);
}
else {
	header('Location: index.php');
	exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
</head>
<body>
	<div>Admin</div>	
</body>
</html>