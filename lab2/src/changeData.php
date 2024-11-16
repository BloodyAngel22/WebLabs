<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://lab4.local");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_COOKIE['auth_token_id']) && isset($_COOKIE['auth_token_pass_hash'])) {
		$auth_token_id = $_COOKIE['auth_token_id'];
		$auth_token_pass_hash = $_COOKIE['auth_token_pass_hash'];

		$connect_data = "host=localhost port=5432 dbname=php_auth user=postgres password=postgres";
		$db_connect = pg_connect($connect_data);
		if (!$db_connect) {
			echo json_encode(['error' => 'Database connection error: ' . pg_last_error($db_connect)]);
			exit;
		}

		$query = "SELECT * FROM users WHERE id = $1 LIMIT 1";
		$result = pg_query_params($db_connect, $query, array($auth_token_id));

		if (!$result) {
			echo json_encode(['error' => 'Query error: ' . pg_last_error($db_connect)]);
			exit;
		}

		if (pg_num_rows($result) > 0) {
			$user = pg_fetch_assoc($result);

			if ($auth_token_id === $user['id'] && $auth_token_pass_hash === $user['password_hash']) {
				$previousPassword = $_POST['previousPassword'] ?? '';
				$checkPasswordResult;
				if (!empty($previousPassword)) {
					$checkPasswordResult = password_verify($previousPassword, $user['password_hash']);

					if ($checkPasswordResult == false) {
						echo json_encode(['error'=> 'Invalid password']);
						exit;
					}
				}

				$firstname = $_POST['firstname'] ?? $user['firstname'];
				$surname = $_POST['surname'] ?? $user['surname'];
				$patronymic = $_POST['patronymic'] ?? $user['patronymic'];

				$new_password_hash = null;
				if (!empty($_POST['password'])) {
					$new_password_hash = password_hash($_POST['password'], PASSWORD_BCRYPT);
				}

				$imageBinary = null;
				if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
					$fileType = mime_content_type($_FILES['image']['tmp_name']);
					if (strpos($fileType, 'image/') !== 0) {
						echo json_encode(['error' => 'Uploaded file is not an image.']);
						exit;
					}

					$imageBinary = file_get_contents($_FILES['image']['tmp_name']);
				}

				$updateFields = [
					"firstname = $1",
					"surname = $2",
					"patronymic = $3"
				];
				$updateValues = [$firstname, $surname, $patronymic];

				if ($new_password_hash) {
					$updateFields[] = "password_hash = $" . (count($updateValues) + 1);
					$updateValues[] = $new_password_hash;
				}

				if ($imageBinary !== null) {
					$updateFields[] = "image = $" . (count($updateValues) + 1);
					$updateValues[] = pg_escape_bytea($imageBinary);
				}

				$updateQuery = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE id = $" . (count($updateValues) + 1);
				$updateValues[] = $auth_token_id;

				$updateResult = pg_query_params($db_connect, $updateQuery, $updateValues);

				if ($updateResult) {
					echo json_encode(['message' => 'User data updated successfully']);
					if ($new_password_hash) {
						setcookie('auth_token_id', $user['id'], time() + 3600 * 24, '/');
						setcookie('auth_token_pass_hash', $new_password_hash, time() + 3600 * 24, '/');
					}
				} else {
					echo json_encode(['error' => 'Failed to update user data: ' . pg_last_error($db_connect)]);
				}
			} else {
				echo json_encode(['error' => 'Invalid authentication token']);
			}
		} else {
			echo json_encode(['error' => 'User not found']);
		}

		pg_close($db_connect);
	} else {
		echo json_encode(['error' => 'Authentication cookies not set']);
	}
} else {
	echo json_encode(['error' => 'Invalid request method']);
}
?>