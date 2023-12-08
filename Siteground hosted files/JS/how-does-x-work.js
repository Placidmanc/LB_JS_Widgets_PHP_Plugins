function toggleSplit(section = 'split1', buttons) {
	for (let i = 1; i <= buttons; i++) {
		let btn = document.getElementById(`splitbtn${i}`);
		let info = document.getElementById(`split${i}info`);

		btn.classList.remove('splitBtnActive');
		info.style.display = 'none';

		if (section === `split${i}`) {
			btn.classList.add('splitBtnActive');
			info.style.display = 'block';
		}
	}
}

window.addEventListener('load', function () {
	// Add a click handler to the buttons
	const buttons = document.getElementsByClassName('hdiw-split-btn');
	Array.from(buttons).forEach((btn, index) => {
		btn.addEventListener('click', function () {
			toggleSplit(`split${index + 1}`, buttons.length);
		});
	});
});
