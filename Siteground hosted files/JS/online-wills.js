window.addEventListener('load', function () {
	/*
	 * Routes are defined by the 2 letter page reference e.g. 'ag',
	 * followed by the path for each button
	 * or a single path if either both buttons go to the same path or if there's a single button
	 *
	 * The buttons (in Elementor) use the Link Options to add attributes.
	 * Attributes are configured as per this example 'data-question|ag,data-answer|yes',
	 * where 'ag' is the page reference and 'yes' is the user choice.
	 */
	const routes = {
		ag: {
			yes: 'location',
			no: 'under-age',
		},
		lo: {
			yes: 'relationship',
			no: 'phone-appointment',
		},
		re: 'children',
		ch: {
			yes: 'special-needs',
			no: 'property',
		},
		sp: {
			yes: 'phone-appointment',
			no: 'equity',
		},
		eq: {
			yes: 'property',
			no: 'phone-appointment',
		},
		pr: {
			yes: 'assets',
			no: 'business',
		},
		as: {
			yes: 'phone-appointment',
			no: 'business',
		},
		bu: {
			yes: 'phone-appointment',
			no: 'onboarding',
		},
	};

	// Get the local storage object
	let qaObject = JSON.parse(localStorage.getItem('owQA') || '{}');

	// Configure the buttons
	const backBtn = document.getElementById('ow-back');
	if (backBtn) {
		backBtn.addEventListener('click', function (event) {
			event.preventDefault();
			window.history.back();
		});
	}

	// Get all buttons that have a 'data-question' attribute and add click listeners
	const buttons = document.querySelectorAll('[data-question]');
	Array.from(buttons).forEach(button => {
		button.addEventListener('click', function (event) {
			event.preventDefault();

			// Get the attribute values
			const q = event.currentTarget.getAttribute('data-question');
			const a = event.currentTarget.getAttribute('data-answer');

			// Update the local storage with the latest answer
			qaObject[q] = a === 'yes' ? 1 : 0;
			localStorage.setItem('owQA', JSON.stringify(qaObject));

			let path;
			if (typeof routes[q] === 'string') {
				path = writeFullPath(routes[q]);
			} else if (typeof routes[q] === 'object') {
				path = writeFullPath(routes[q][a]);
			}

			if (path) {
				window.location.href = path;
			}
		});
	});

	// Add functionality to the 'Take Quiz Again' button
	const retake = document.getElementById('retake');
	if (retake) {
		retake.addEventListener('click', function () {
			clearStorage();
			window.location.href = '/online-wills/age-check/';
		});
	}

	const clearStorage = function () {
		localStorage.removeItem('owQA');
	};

	const stepHandler = function (pageRef) {
		// Get the values that can change the count
		const chVal = qaObject['ch'];
		const prVal = qaObject['pr'];

		const pageRefToQuestionNumber = {
			pr: chVal ? 7 : 5,
			as: chVal ? 8 : 6,
			bu: chVal && prVal ? 9 : !chVal && prVal ? 7 : chVal && !prVal ? 8 : 6,
		};

		const questionNumber = pageRefToQuestionNumber[pageRef];
		if (questionNumber !== undefined) {
			updateQuestionText(questionNumber);
		}
	};

	const writeFullPath = function (route) {
		return `/online-wills/${route}`;
	};

	const updateQuestionText = function (q) {
		document.getElementById('pageRef').innerHTML = `<h3 class="questionNum">QUESTION ${q}</h3>`;
	};

	// Get the component (Question heading in Elementor) then get it's attributes
	const pageRefEl = document.getElementById('pageRef');
	if (pageRefEl) {
		const pageRef = pageRefEl.getAttribute('data-pageref');
		if (pageRef === 'pr' || pageRef === 'as' || pageRef === 'bu') {
			stepHandler(pageRef);
		}
	}
});
