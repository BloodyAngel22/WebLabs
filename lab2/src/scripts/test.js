fetch('http://localhost/pgsqlTest.php')
	.then(response => {
		return response.json();
	})
	.then(data => {
		console.log(data);
	})
	.catch(error => {
		console.log(error);
	})
