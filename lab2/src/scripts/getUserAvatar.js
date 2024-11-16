const getUserAvatar = async (userId) => {
	try {
		const response = await fetch('getImage.php?id=' + userId);

		if (!response.ok) {
			throw new Error(`HTTP error! status: ${response.status}`);
		}
		const blob = await response.blob();
		if (blob.size === 0) {
			return null;
		}
		const url = URL.createObjectURL(blob);
		return url;
	} catch (error) {
		console.error('Fetch error:', error);
	}
}

export { getUserAvatar };