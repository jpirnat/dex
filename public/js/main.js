const saveFormatButton = document.getElementById('save-format');
if (saveFormatButton) {
	saveFormatButton.addEventListener('click', function() {
		const formatIdentifier = this.dataset.formatIdentifier;
		const formatName = this.dataset.formatName;
		const rating = this.dataset.rating;

		const date = new Date();
		date.setFullYear(date.getFullYear() + 5);
		const expires = date.toUTCString();

		document.cookie = `format=${formatIdentifier}; path=/; expires=${expires}`;
		document.cookie = `rating=${rating}; path=/; expires=${expires}`;

		Swal.fire({
			type: 'success',
			html: `${formatName} [${rating}] has been saved as your default `
				+ 'format. <a href="/stats/current">Current Stats</a> will now '
				+ `always lead to the latest data for ${formatName} [${rating}].`
		});

		this.remove();
	});
}
