const getUserData = async () => {
	try {
		const response = await fetch('user.php', {
			method: 'GET',
			credentials: 'include',
			headers: {
				'Content-Type': 'application/json',
			},
		});
		const data = await response.json();
		console.log(data);
		return data;
	}	catch (error) {
		console.error('Fetch error:', error);
	}
}

export { getUserData };