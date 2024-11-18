<?php
header("Access-Control-Allow-Credentials: true");

//Не безопасно
// if (empty($_COOKIE["email"]) || empty($_COOKIE["reset-url"])) {
// 	header("Location: index.php");
// 	exit;
// }
if (empty($_COOKIE["id"]) || empty($_COOKIE["reset-url"])) {
	header("Location: index.php");
	exit;
}

$connect_data = "host=localhost port=5432 dbname=php_auth user=postgres password=postgres";
$conn = pg_connect($connect_data);

if (!$conn) {
	header("Location: index.php");
	exit;
}
$id = $_COOKIE["id"];
$query = "SELECT COUNT(*) FROM users WHERE id = $1";
$result = pg_query_params($conn, $query, array($id));

if (!$result) {
	header("Location: index.php");
	exit;
}

$row = pg_fetch_row($result);
if ($row[0] == 0) {
	header("Location: index.php");
} 
pg_free_result($result);
pg_close($conn);
//TODO: сделать проверку на существующего пользователя и подумать насчет валидации url.

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Reset password</title>
	<style>
		:root {
			font-size: calc(16px + 0.5vw);
		}
		body {
			font-family: Arial, sans-serif;
			background-color: #f9f9f9;
			margin: 0;
			padding: 0;
			display: flex;
			justify-content: center;
			align-items: center;
			height: 100vh;
		}

		.container {
			background-color: #fff;
			padding: 30px;
			border-radius: 8px;
			box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
			width: 100%;
			max-width: 500px;
		}

		h1 {
			font-size: 2rem;
			text-align: center;
			margin-bottom: 30px;
			margin-top: 0;
		}

		form {
			width: 100%;
			display: flex;
			flex-direction: column;
		}

		form div {
			margin-bottom: 15px;
		}

		label {
			font-size: 1rem;
			margin-bottom: 5px;
			display: block;
		}

		input[type="password"] {
			display: flex;
			padding: 10px;
			border: 1px solid #ccc;
			border-radius: 4px;
			font-size: 1rem;
		}

		button {
			padding: 10px;
			background-color: #007bff;
			color: #fff;
			border: none;
			border-radius: 4px;
			cursor: pointer;
			font-size: 1.2rem;
		}

		button:hover {
			background-color: #0056b3;
		}

		@media (max-width: 600px) {
			.container {
				padding: 15px;
			}

			h1 {
				font-size: 20px;
			}

			label, input, button {
				font-size: 14px;
			}
		}
	</style>
</head>
<body>
	<div class="container">
		<h1>Reset Password</h1>
		<form action="" id="reset-password-form">
			<div>
				<label for="password">Password:</label>
				<input type="password" placeholder="Password" id="password" required>
			</div>
			<div>
				<label for="password-again">Password again:</label>
				<input type="password" placeholder="Password" id="password-again" required>
			</div>
			<div>
				<button type="submit">Apply</button>
			</div>
		</form>
	</div>	
</body>
<script type="module">
	const resetPasswordForm = document.getElementById("reset-password-form");
	resetPasswordForm.addEventListener("submit", async (event) => {
		event.preventDefault();

		const password = document.getElementById("password").value;
		const passwordAgain = document.getElementById("password-again").value;

		if (password !== passwordAgain) {
			alert("Пароли не совпадают");
			return;
		}

		try {
			const response = await fetch('changePassword.php', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				credentials:"include",
				body: JSON.stringify({ password })
			});

			const result = await response.json();
			if (result.success) {
				alert("Пароль успешно изменён");
				window.location.href = 'index.php';
			} else {
				alert(result.error || "Произошла ошибка при изменении пароля");
			}
		} catch (error) {
			console.error('Ошибка:', error);
			alert("Произошла ошибка при изменении пароля");
		}
	});
</script>
</html>