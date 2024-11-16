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
	exit;
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

		case 'forgot':
			forgot();
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

	if (!isset($input['username']) || !isset($input['password']) || !isset($input['email'])) {
		echo json_encode(['error' => 'All fields are required']);
		exit;
	}

	$email = pg_escape_string($db_connect, $input['email']) ?? '';

	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		echo json_encode(['error' => 'Invalid email']);
		exit;
	}

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

	$hashed_password = password_hash($input['password'], PASSWORD_BCRYPT);

	$ip = $_SERVER['REMOTE_ADDR'];

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

	//FIXME: Не безопасно
	// $username = $input['username'];

	$username = pg_escape_string($db_connect, $input['username']) ?? '';
	$password = pg_escape_string($db_connect, $input['password']) ?? '';

	$query = "SELECT * FROM users WHERE username = '$username'";

	if (!$result = pg_query($db_connect, $query)) {
		echo json_encode(['error' => 'Error: ' . pg_last_error($db_connect)]);
		exit;
	}

	if ($result && pg_num_rows($result) > 0) {
		$user = pg_fetch_assoc($result);

		if (password_verify($password, $user['password_hash'])) {
			//Cookies
			setcookie('auth_token_id', $user['id'], [
				'expires' => time() + 3600 * 24,
				'path' => '/',

			]);

			setcookie('auth_token_pass_hash', $user['password_hash'], [
				'expires' => time() + 3600 * 24,
				'path' => '/',

			]);

			echo json_encode(['message'=> 'User logged in successfully']);

			return;
		} else {
			echo json_encode(['error' => 'Invalid password']);
			return; 
		}

	} else {
		echo json_encode(['error' => 'User not found']);
		return;
	}
}

function logout()
{
	//Cookies
	setcookie('auth_token_id', "", time() - 3600 * 24, '/');
	setcookie('auth_token_pass_hash', "", time() - 3600 * 24, '/');

	setcookie("email", "", time() - 3600 / 2,"/");
	setcookie("reset-url", "", time() - 3600 / 2,"/");

	echo json_encode(['message' => 'User logged out successfully']);
}


function generateRandomString($length = 10)
{
	$randomized = new \Random\Randomizer();
	$length = 20;
	$randomString = $randomized->getBytesFromString('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', $length);
	return $randomString;
}

function forgot()
{
	$input = json_decode(file_get_contents('php://input'), true);
	$email = $input['email'];

	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		echo json_encode(['error'=> 'Not valid email']);
		return;
	}

	$resetToken = generateRandomString(16);
	$url = "http://lab4.local:5005/reset_password.php?token=" . $resetToken;

	$userId = getUserIdByEmail($email);
	setcookie("id", $userId, time() + 3600 / 2,"/");
	//Не безопасно
	// setcookie("email", $email, time() + 3600 / 2,"/");
	setcookie("reset-url", $url, time() + 3600 / 2,"/");

	echo json_encode(['message' => 'The password recovery link has been successfully created. Redirect to the link?', 'url' => $url]);
}

function getUserIdByEmail($email)
{
	global $db_connect;
	$query = "SELECT id FROM users WHERE email = $1";
	$result = pg_query_params($db_connect, $query, array($email));

	if (!$result) {
		echo json_encode(["error"=> "Error: " . pg_last_error($db_connect)]);
		exit;
	}

	if (pg_num_rows($result) == 0) {
		echo json_encode(["error"=> "User not found"]);
		exit;
	}
	$row = pg_fetch_assoc($result);
	$user = $row;

	return $user['id'];
}

pg_close($db_connect);
?>