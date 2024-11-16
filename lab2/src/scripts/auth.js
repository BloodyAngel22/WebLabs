const registerUser = async (username, password, email) => {
	try {
		const response = await fetch("Auth.php", {
			method: "POST",
			headers: {
				"Content-Type": "application/json",
			},
			body: JSON.stringify({
				action: "register",
				username: username,
				password: password,
				email: email,
			}),
		});

		if (!response.ok) {
			throw new Error(`HTTP error! status: ${response.status}`);
		}

		const data = await response.json();
		console.log(data);
		return data; 
	} catch (error) {
		console.error('Fetch error:', error);
	}
}

const loginUser = async (username, password) => {
	try {
		const response = await fetch('Auth.php', {
			method: 'POST',
			credentials: 'include',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify({ username: username, password: password, action: "login" }),
		});

		const data = await response.json();
		console.log(data);

		return data;
	} catch (error) {
		console.error('Ошибка:', error.message);
	}
}

const getSomeData = async () => {
	try {
		const response = await fetch('Auth.php', {
			method: 'GET',
			credentials: 'include',
			headers: {
				'Content-Type': 'application/json',
			},
		});
		const data = await response.json();
		console.log(data);
	} catch (error) {
		console.error('Fetch error:', error);
	}
}

const logoutUser = async () => {
	try {
		const response = await fetch('Auth.php', {
			method: 'POST',
			credentials: 'include',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify({ action: "logout" }),
		});
		const data = await response.json();
		console.log(data);
		return data;
	} catch (error) {
		console.error('Fetch error:', error);
	}
}

const forgotPassword = async (email) => {
	try {
		const response = await fetch('Auth.php', {
			method: 'POST',
			credentials: 'include',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify({ action: "forgot", email: email })
		});
		const data = await response.json();
		console.log(data);
		return data;
	} catch (error) {
		console.error('Fetch error:', error);
	}
}

export { registerUser, loginUser, getSomeData, logoutUser, forgotPassword };
