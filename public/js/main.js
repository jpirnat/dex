const saveFormatButton = document.getElementById('save-format');
if (saveFormatButton) {
	saveFormatButton.addEventListener('click', function() {
		const format = this.dataset.format;
		const rating = this.dataset.rating;
	
		const date = new Date();
		date.setFullYear(date.getFullYear() + 5);
		const expires = date.toUTCString();
	
		document.cookie = `format=${format}; path=/; expires=${expires}`;
		document.cookie = `rating=${rating}; path=/; expires=${expires}`;
	
		this.remove();
	});
}
