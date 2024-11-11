<?php
header("Access-Control-Allow-Origin: http://lab4.local");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	header("Access-Control-Allow-Origin: http://lab4.local");
	header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
	header("Access-Control-Allow-Headers: Content-Type");
	header("Access-Control-Allow-Credentials: true");
	header("Content-Type: application/json");
	exit; // Завершите выполнение скрипта для OPTIONS запроса
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$connect_data = "host=localhost port=5432 dbname=php_auth user=postgres password=postgres";
$db_connect = pg_connect($connect_data);
if (!$db_connect) {
	echo json_encode(['error' => 'Database connection error: ' . pg_last_error($db_connect)]);
	exit;
}

try {
	$inputData = json_decode(file_get_contents('php://input'), true);

	if (json_last_error() !== JSON_ERROR_NONE) {
		http_response_code(400);
		throw new Exception('Invalid JSON input' . json_last_error_msg());
	}

	$action = $inputData['action'] ?? '';

	switch ($action) {
		case 'register':
			register();
			break;

		case 'login':
			login();
			break;

		case 'logout':
			logout();
			break;

		case 'reset_password':
			reset_password();
			break;

		default:
			echo json_encode(['error' => 'Invalid action ' . $action]);
			exit;
	}
} catch (Exception $e) {
	echo json_encode(['error' => $e->getMessage()]);
	exit;
}

function register()
{
	global $db_connect;
	$input = json_decode(file_get_contents('php://input'), true);

	$username = pg_escape_string($db_connect, $input['username']) ?? '';

	// Проверка на существование пользователя
	$query = "SELECT * FROM users WHERE username = '$username'";
	$result = pg_query($db_connect, $query);

	if (!$result) {
		echo json_encode(['error' => 'Error: ' . pg_last_error($db_connect)]);
		exit;
	}

	if (pg_num_rows($result) > 0) {
		echo json_encode(['error' => 'User already exists']);
		exit;
	}

	// Проверка на пустые поля
	if (!isset($input['username']) || !isset($input['password']) || !isset($input['email'])) {
		echo json_encode(['error' => 'All fields are required']);
		exit;
	}

	$email = pg_escape_string($db_connect, $input['email']) ?? '';

	// Проверка на валидность email
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		echo json_encode(['error' => 'Invalid email']);
		exit;
	}

	// Проверка на существование email
	$query = "SELECT * FROM users WHERE email = '$email'";
	$result = pg_query($db_connect, $query);

	if (!$result) {
		echo json_encode(['error' => 'Error: ' . pg_last_error($db_connect)]);
		exit;
	}

	if (pg_num_rows($result) > 0) {
		echo json_encode(['error' => 'Email already exists']);
		exit;
	}

	// Хеширование пароля
	$hashed_password = password_hash($input['password'], PASSWORD_BCRYPT);

	// Получаем IP пользователя
	$ip = $_SERVER['REMOTE_ADDR'];

	// Вставка нового пользователя в базу данных
	$query = "INSERT INTO users (username, password_hash, email, ip) VALUES ('$username', '$hashed_password', '$email', '$ip')";

	if (!pg_query($db_connect, $query)) {
		echo json_encode(['error' => 'Error: ' . pg_last_error($db_connect)]);
		exit;
	}

	echo json_encode(['message' => 'User registered successfully']);
}

function login()
{
	global $db_connect;

	$input = json_decode(file_get_contents('php://input'), true);

	$username = pg_escape_string($db_connect, $input['username']) ?? '';
	$password = pg_escape_string($db_connect, $input['password']) ?? '';

	// Поиск пользователя по имени
	$query = "SELECT * FROM users WHERE username = '$username'";

	if (!$result = pg_query($db_connect, $query)) {
		echo json_encode(['error' => 'Error: ' . pg_last_error($db_connect)]);
		exit;
	}

	if ($result && pg_num_rows($result) > 0) {
		$user = pg_fetch_assoc($result);

		// Проверка пароля
		if (password_verify($password, $user['password_hash'])) {
			//Cookies
			setcookie('auth_token_id', $user['id'], [
				'expires' => time() + 3600 * 24,
				'path' => '/',
				'secure' => false,
				'sameSite' => 'Lax',
			]);

			setcookie('auth_token_pass_hash', $user['password_hash'], [
				'expires' => time() + 3600 * 24,
				'path' => '/',
				'secure' => false,
				'sameSite' => 'Lax',
			]);

			echo json_encode(['message'=> 'User logged in successfully']);

			return; // Завершаем выполнение функции
		} else {
			echo json_encode(['error' => 'Invalid password']);
			return; // Завершаем выполнение функции
		}

	} else {
		echo json_encode(['error' => 'User not found']);
		return; // Завершаем выполнение функции
	}
}

function logout()
{
	//Cookies
	setcookie('auth_token_id', "", time() - 3600 * 24, '/'); // Удаление куки
	setcookie('auth_token_pass_hash', "", time() - 3600 * 24, '/');

	echo json_encode(['message' => 'User logged out successfully']);
}

function reset_password()
{
	// Логика сброса пароля (не реализована)
}

// Закрываем соединение с базой данных
pg_close($db_connect);
?>