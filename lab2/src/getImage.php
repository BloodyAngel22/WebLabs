<?php
header("Content-Type: image/jpeg");
header("Access-Control-Allow-Origin: http://lab4.local");

if (isset($_GET['id'])) {
	$userId = $_GET['id'];

	$connect_data = "host=localhost port=5432 dbname=php_auth user=postgres password=postgres";
	$db_connect = pg_connect($connect_data);
	if (!$db_connect) {
		http_response_code(500);
		exit;
	}

	$query = "SELECT image FROM users WHERE id = $1";
	$result = pg_query_params($db_connect, $query, array($userId));

	if (!$result) {
		http_response_code(500);
		exit;
	}

	if (pg_num_rows($result) > 0) {
		$user = pg_fetch_assoc($result);
		$imageData = pg_unescape_bytea($user['image']);

		if ($imageData == null) {
			echo '';
		}

		echo $imageData;
	} else {
		http_response_code(404);
	}

	pg_close($db_connect);
} else {
	http_response_code(400);
}
?>