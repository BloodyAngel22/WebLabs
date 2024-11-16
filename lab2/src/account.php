<?php
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

	} else {
		header('Location: index.php');
		exit;
	}

	pg_close($db_connect);
} else {
	header('Location: index.php');
	exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Account</title>
</head>
<style>
	:root {
		font-size: 24px;
	}

	* {
		margin: 0;
		padding: 0;
		font-family: 'Poppins', sans-serif;
	}

	span {
		font-weight: 600;
		font-size: 1.1rem;
	}

	body {
		background: rgb(226, 238, 238);
		background: linear-gradient(131deg, rgba(226, 238, 238, 1) 0%, rgba(80, 79, 75, 1) 84%);
	}

	h1 {
		margin-bottom: 0.6rem;
	}

	.container {
		min-height: 100vh;
		height: auto;
		width: 100vw;
		display: flex;
		flex-direction: column;
		align-items: center;
	}

	.change {
		margin-top: 1rem;
	}

	.change>h3 {
		margin-bottom: 0.6rem;
		text-align: center;
	}

	.change>form {
		display: flex;
		flex-direction: column;
		gap: 0.6rem;
		width: max-content;
	}

	.change>form>div {
		display: flex;
		gap: 0.8rem;
		align-items: center;
	}

	.change-btn {
		width: max-content;
		padding: 0.25rem 0.5rem;
		border-radius: 1rem;
		border: none;
		transition: all 0.2s ease-in-out;
		cursor: pointer;
	}

	.change-btn:hover {
		scale: 1.1;
	}

	.change-btn:active {
		scale: 0.9;
	}

	.modal-btns {
		display: flex;
		justify-content: center;
		flex-direction: column;
	}

	input {
		padding: 6px 12px;
		font-size: 16px;
		font-weight: 400;
		line-height: 1.5;
		color: #212529;
		background-color: #fff;
		background-clip: padding-box;
		border: 1px solid #ced4da;
		appearance: none;
		border-radius: 4px;
		transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;

		:focus {
			color: #212529;
			background-color: #fff;
			border-color: #86b7fe;
			outline: 0;
			box-shadow: 0 0 0 0.25rem rgb(13 110 253 / 25%);
		}

	}

	a.redirect {
		text-decoration: none;
		color: inherit;
	}

	img {
		width: 3rem;
		height: 3rem;
		border-radius: 50%;
		object-fit: cover;
	}
</style>

<body>

	<div class="container">
		<h1>Account</h1>
		<p>Hello, <span><?php echo $user['username']; ?></span></p>
		<div class="change">
			<h3>Сменить данные</h3>
			<form action="" method="post">
				<div>
					<label for="previous-password">Previous password</label>
					<input type="password" id="previous-password" placeholder="Password">
				</div>
				<div>
					<label for="change-password">New Password</label>
					<input type="password" id="change-password" placeholder="Password">
				</div>
				<div>
					<label for="check-password">Check password</label>
					<input type="password" id="check-password" placeholder="Password">
				</div>
				<div>
					<label for="change-firstname">First name</label>
					<input type="text" id="change-firstname" placeholder="First name">
				</div>
				<div>
					<label for="change-surname">Surname</label>
					<input type="text" id="change-surname" placeholder="Surname">
				</div>
				<div>
					<label for="change-patronymic">Patronymic</label>
					<input type="text" id="change-patronymic" placeholder="Patronymic">
				</div>
				<div>
					<label for="current-avatar">Current avatar</label>
					<img src="" alt="none" id="current-avatar">
				</div>
				<div>
					<label for="change-avatar">New Avatar</label>
					<input type="file" id="change-avatar" placeholder="Avatar">
					<img id="img-avatar" src="" alt="none">
				</div>
				<div class="modal-btns">
					<button class="change-btn" type="submit">Change</button>
					<a class="redirect" href="index.php">На главную</a>
				</div>
			</form>
		</div>
	</div>

	<script type="module">
		import { getUserAvatar } from '/scripts/getUserAvatar.js';
		const previousPassword = document.getElementById('previous-password');
		const changeFirstname = document.getElementById('change-firstname');
		const changeSurname = document.getElementById('change-surname');
		const changePatronymic = document.getElementById('change-patronymic');
		const changePassword = document.getElementById('change-password');
		const checkPassword = document.getElementById('check-password');
		const avatar = document.getElementById('change-avatar');
		const currentAvatar = document.getElementById('current-avatar');

		document.addEventListener('DOMContentLoaded', async () => {
			changeFirstname.value = '<?php echo $user['firstname']; ?>';
			changeSurname.value = '<?php echo $user['surname']; ?>';
			changePatronymic.value = '<?php echo $user['patronymic']; ?>';

			const avatarUrl = await getUserAvatar('<?php echo $user['id']; ?>');
			currentAvatar.src = avatarUrl;
		})

		avatar.addEventListener('change', () => {
			const imgAvatar = document.getElementById('img-avatar');
			imgAvatar.src = URL.createObjectURL(avatar.files[0]);
		})

		document.querySelector('form').addEventListener('submit', async (event) => {
			event.preventDefault();

			if (changePassword.value !== checkPassword.value) {
				changePassword.value = '';
				checkPassword.value = '';
				alert('Пароли не совпадают!');
				return;
			}

			const data = await sendChangeData(changeFirstname.value, changeSurname.value, changePatronymic.value, changePassword.value, avatar.files[0]);
			console.log(data);

			if (data.error) {
				alert(data.error);
			}

			if (data.message) {
				const avatarUrl = await getUserAvatar('<?php echo $user['id']; ?>');
				currentAvatar.src = avatarUrl;
				avatar.value = '';
				const imgAvatar = document.getElementById('img-avatar');
				imgAvatar.src = '';
				alert(data.message);
			}
		});

		const sendChangeData = async (changeFirstname, changeSurname, changePatronymic, changePassword, avatarFile) => {
			try {
				const formData = new FormData();
				formData.append('previousPassword', previousPassword.value);
				formData.append('firstname', changeFirstname);
				formData.append('surname', changeSurname);
				formData.append('patronymic', changePatronymic);
				formData.append('password', changePassword);

				if (avatarFile) {
					formData.append('image', avatarFile);
					console.log(avatarFile);
				}

				const response = await fetch('changeData.php', {
					method: 'POST',
					body: formData,
					credentials: 'include'
				});

				const data = await response.json();
				return data;
			} catch (error) {
				console.error(error);
				alert('Произошла ошибка!');
			}
		}

	</script>
</body>

</html>