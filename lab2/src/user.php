<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://lab4.local");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");

if (isset($_COOKIE['auth_token_id']) && isset($_COOKIE['auth_token_pass_hash'])) {
	$auth_token_id = $_COOKIE['auth_token_id'];
	$auth_token_pass_hash = $_COOKIE['auth_token_pass_hash'];

	$connect_data = "host=localhost port=5432 dbname=php_auth user=postgres password=postgres";
	$db_connect = pg_connect($connect_data);
	if (!$db_connect) {
		echo json_encode(['error' => 'Database connection error: ' . pg_last_error($db_connect)]);
		exit;
	}

	$query = "SELECT * FROM users WHERE id = '$auth_token_id' LIMIT 1";
	$result = pg_query($db_connect, $query);

	if (!$result) {
		echo json_encode(['error' => 'Query error: ' . pg_last_error($db_connect)]);
		exit;
	}

	if (pg_num_rows($result) > 0) {
		$user = pg_fetch_assoc($result);

		if ($auth_token_pass_hash === $user['password_hash']) {
			echo json_encode(['message' => 'Hello, ' . $user['username'] . '!']);
		} else {
			echo json_encode(['error' => 'Invalid authentication token.']);
		}
	} else {
		echo json_encode(['error' => 'User not found.']);
	}

	pg_close($db_connect);
} else {
	echo json_encode(['error' => 'Authentication cookies not set.']);
}
?>