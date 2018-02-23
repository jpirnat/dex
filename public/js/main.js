// When the user clicks an a.scroll anchor, smooth scroll to the destination.
let anchors = Array.from(document.getElementsByClassName('scroll'));
anchors.forEach(function(anchor) {
	anchor.addEventListener('click', function(event) {
		event.preventDefault();
		let targetId = anchor.getAttribute('href').substring(1); // removes '#'
		let target = document.getElementById(targetId);
		target.scrollIntoView({
			block: 'start',
			inline: 'nearest',
			behavior: 'smooth'
		});
	});
});
