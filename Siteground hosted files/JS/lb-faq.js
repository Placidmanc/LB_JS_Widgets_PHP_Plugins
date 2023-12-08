function toggleFAQ(index = 1, buttons) {
	for (let i = 1; i <= buttons; i++) {
		let btn = document.getElementById(`lb-faq-btn-${i}`);
		let info = document.getElementById(`lb-faq-content-${i}`);

		btn.classList.remove('faqBtnActive');
		info.style.display = 'none';

		if (index === i) {
			btn.classList.add('faqBtnActive');
			info.style.display = 'block';
		}
	}
}

window.addEventListener('load', function () {
	// Add a click handler to the buttons
	const buttons = document.getElementsByClassName('lb-faq-btn');
	Array.from(buttons).forEach((btn, index) => {
		btn.addEventListener('click', function () {
			toggleFAQ(index + 1, buttons.length);
		});
	});
});
