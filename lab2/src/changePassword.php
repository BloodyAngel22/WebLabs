<?php
header("Access-Control-Allow-Origin: http://lab4.local");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$input = json_decode(file_get_contents('php://input'), true);
	if (!empty($input['password'])) {
		changePassword($input['password']);
	}
}

function changePassword($newPassword)
{
	$connect_data = "host=localhost port=5432 dbname=php_auth user=postgres password=postgres";
	$db_connect = pg_connect($connect_data);
	if (!$db_connect) {
		echo json_encode(['error' => 'Не удалось подключиться к базе данных']);
		http_response_code(500);
		return;
	}

	//FIXME: Не безопасно
	// $email = $_COOKIE["email"];

	$hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

	//Не безопасно
	// $query = "UPDATE users SET password_hash = $1 WHERE email = $2"; 
	// $result = pg_query_params($db_connect, $query, array($hashedPassword, $email));

	//Безопаснее
	$id = $_COOKIE["id"];
	$query = "UPDATE users SET password_hash = $1 WHERE id = $2";
	$result = pg_query_params($db_connect, $query, array($hashedPassword, $id));

	if (!$result) {
		echo json_encode(['error' => 'Ошибка выполнения запроса: ' . pg_last_error($db_connect)]);
		http_response_code(500);
		pg_close($db_connect);
		return;
	}

	if (pg_affected_rows($result) > 0) {
		// Удаление куков после изменения пароля
		// setcookie("email", "", time() - 3600, "/");

		setcookie('id', "", time() - 3600, '/');
		setcookie("reset-url", "", time() - 3600, "/");
		echo json_encode(['success' => true]);
	} else {
		echo json_encode(['error' => 'Не удалось изменить пароль']);
	}

	pg_close($db_connect);
}

?>