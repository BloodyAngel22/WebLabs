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
				$data = json_decode(file_get_contents('php://input'), true);

				$firstname = isset($data['firstname']) ? $data['firstname'] : $user['firstname'];
				$surname = isset($data['surname']) ? $data['surname'] : $user['surname'];
				$patronymic = isset($data['patronymic']) ? $data['patronymic'] : $user['patronymic'];

				$updateFields = [];
				$updateValues = [$firstname, $surname, $patronymic];

				$updateQuery = "UPDATE users SET 
                                firstname = $1, 
                                surname = $2, 
                                patronymic = $3";

				$new_password_hash = null;
				if (!empty($data['password'])) {
					$new_password_hash = password_hash($data['password'], PASSWORD_BCRYPT);
					$updateQuery .= ", password_hash = $4";
					$updateValues[] = $new_password_hash;
				}

				$updateQuery .= " WHERE id = $" . (count($updateValues) + 1);
				$updateValues[] = $auth_token_id;

				$updateResult = pg_query_params($db_connect, $updateQuery, $updateValues);

				if ($updateResult) {
					$response = [
						'message' => 'User data updated successfully',
						'data' => [
							'firstname' => $firstname,
							'surname' => $surname,
							'patronymic' => $patronymic,
							'password_changed' => !empty($data['password'])
						]
					];

					if ($new_password_hash) {
						// Обновляем куки с новым хешем пароля и id
						setcookie('auth_token_id', $_COOKIE['auth_token_id'], time() + 3600 * 24, "/", );
						setcookie('auth_token_pass_hash', $new_password_hash, time() + 3600 * 24, "/", ); 
						$response['new_auth_token'] = $new_password_hash;
					}

					echo json_encode($response);
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