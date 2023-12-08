function toggleWho(index = 1, buttons) {
	for (let i = 1; i <= buttons; i++) {
		let info = document.getElementById(`who-content-${i}`);

		info.style.display = index === i ? 'block' : 'none';
	}
}

window.addEventListener('load', function () {
	// Add a click handler to the buttons
	const buttons = document.getElementsByClassName('who-btn');
	Array.from(buttons).forEach((btn, index) => {
		btn.addEventListener('click', function () {
			toggleWho(index + 1, buttons.length);
		});
	});
});
