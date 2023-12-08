document.addEventListener('DOMContentLoaded', function () {
	//console.log('ACF WILLS FORM ');

	var currentIndex = 0; // Track the current form part to show - default is 0
	var visitedForms = [0]; // Hold the form indexes for the back button
	var minKeyCount = 3; // Define the minimum number of keystrokes required for textareas
	var hasOverlaysIndexes = []; // Hold the overlays indexes
	var overlayShown = false; // Prevent overlay showing more than once

	// Initially hide all forms except the first one
	var helpContainer = document.querySelector('#help-container');
	var sectionTitleContainer = document.querySelector('#section-title');
	var formParts = document.querySelectorAll('#form-part');

	formParts.forEach(function (part, index) {
		if (index !== currentIndex) {
			part.style.display = 'none';
		}

		// Move the form elements to their new containers
		var helpTitles = part.querySelectorAll('#wills-form .help-title .acf-input');
		var helpTexts = part.querySelectorAll('#wills-form .help-text .acf-input');
		var sectionTitles = part.querySelectorAll('#wills-form .section-label');

		moveElements(helpTitles, helpContainer, 'help-title', index, false);
		moveElements(helpTexts, helpContainer, 'help-text', index, false);
		moveElements(sectionTitles, sectionTitleContainer, 'section-title', index, false, false);

		// Move inner form elements that are only shown with conditional logic
		var innerFormParts = part.querySelectorAll('#form-part-inner');

		if (innerFormParts.length > 0) {
			innerFormParts.forEach(function (innerpart, indx) {
				var innerHelpTitles = innerpart.querySelectorAll(
					'#form-part-inner .inner-help-title .acf-input',
				);
				var innerHelpTexts = innerpart.querySelectorAll(
					'#form-part-inner .inner-help-text .acf-input',
				);
				var innerTitles = innerpart.querySelectorAll('#form-part-inner .inner-section-label');

				// Add 100 (relates to number of forms - increase if over 100) to the index to make it easier to set the visibility
				var innerIndex = 100 + indx;

				moveElements(innerHelpTitles, helpContainer, 'inner-help-title', innerIndex, true);
				moveElements(innerHelpTexts, helpContainer, 'inner-help-text', innerIndex, true);
				moveElements(
					innerTitles,
					sectionTitleContainer,
					'inner-section-title',
					innerIndex,
					true,
					false,
				);
			});
		}
	});

	// Configure the next, back and help-close buttons
	var nextButtonContainer = document.querySelector('#next-button-container');
	var backButton = document.querySelector('#back-button');

	document.querySelector('.help-close-button a').addEventListener('click', function (event) {
		event.preventDefault();
		toggleHelpBoxVisibility();
	});

	document.querySelector('#next-button').addEventListener('click', function (event) {
		event.preventDefault();
		handleNext();
	});

	backButton.addEventListener('click', function (event) {
		event.preventDefault();
		handleBack();
	});

	// Add event listener for key presses - Enter acts like Next
	document.addEventListener('keyup', function (event) {
		if (event.key === 'Enter') handleNext();
	});

	// Handle Next button and Enter key
	function handleNext() {
		// Prevent Enter key firing if next button is hidden (indicates form not ready)
		if (nextButtonContainer.style.display === 'flex') {
			// Get the next form part index
			var nextIndex = getNextFormPartIndex(currentIndex, 'next');

			// Check if nextIndex is a valid index before trying to show the next part
			if (nextIndex !== undefined && nextIndex >= 0 && nextIndex < formParts.length) {
				currentIndex = nextIndex;
				showFormPart(currentIndex);
			}
		}
	}

	// Handle Back button
	function handleBack() {
		var previousIndex = getBackFormIndex();

		currentIndex = previousIndex;
		showFormPart(currentIndex, true);
	}

	// When going back, we take the last index from the visitedForms array
	function getBackFormIndex() {
		visitedForms.pop(); // Remove the current form from the visited history
		return (previousIndex = visitedForms.length > 0 ? visitedForms[visitedForms.length - 1] : 0);
	}

	function getNextFormPartIndex(currentIndex) {
		// Get the current form part
		var currentFormPart = formParts[currentIndex];
		var nextIndex = currentIndex + 1;

		// Initialize variables to store the field and its value
		var comparisonType = 'select';
		var fieldValue = '';

		// Try to get a select field
		var selectField = currentFormPart.querySelector('.select-question select');
		if (selectField) {
			fieldValue = selectField.value;
		}

		// If not a select, try to get a true/false field
		if (!fieldValue) {
			var trueFalseField = currentFormPart.querySelector('.acf-true-false input');
			if (trueFalseField) {
				comparisonType = 'true_false';
				fieldValue = trueFalseField.value;
			}
		}

		// If not a true/false field, try to get a custom radio field
		if (!fieldValue) {
			var selectedRadio = currentFormPart.querySelector('.custom-radio.selected');
			if (selectedRadio) {
				comparisonType = 'radio';
				fieldValue = selectedRadio.getAttribute('data-value');
			}
		}

		// Get the values to handle how to move or skip ahead to the next form
		var customChoices = currentFormPart.querySelector('.conditional-choices input');
		var customSteps = currentFormPart.querySelector('.conditional-choices-steps input');

		if (fieldValue && customChoices) {
			var customValue = customChoices.value;
			var stepsValue = customSteps !== null ? parseInt(customSteps.value, 10) : 1;

			// Based on the value, determine the next form part index
			nextIndex = skipFormLogic(comparisonType, fieldValue, customValue, stepsValue, currentIndex);
		}

		return nextIndex;
	}

	// Handle the form logic to either move to next form or skip to another form
	function skipFormLogic(comparisonType, fieldValue, customValue, stepsValue, currentIndex) {
		if (
			(comparisonType === 'select' && customValue.includes(fieldValue)) ||
			(comparisonType === 'true_false' && customValue === fieldValue) ||
			(comparisonType === 'radio' && customValue.includes(fieldValue))
		) {
			return currentIndex + stepsValue; // Skip the next form to the stepValue form
		}
		return currentIndex + 1; // Proceed to the next part
	}

	// Show a specific part of the form
	function showFormPart(index, isBack = false) {
		// Add index to track navigation
		if (!isBack) visitedForms.push(index);

		// Hide the back button on the first form
		if (index === 0) {
			backButton.style.visibility = 'hidden';
		} else {
			backButton.style.visibility = 'visible';
		}

		formParts.forEach(function (part, i) {
			// Display logic for form parts
			if (i === index) {
				part.style.display = 'flex';

				// Insert the HTML for the Yes decision on 'need an urgent will'
				var urgentWillBox = part.querySelector('#urgent-will-yes');

				if (urgentWillBox) {
					// Create urgent will page content
					createUrgentWillHTML(urgentWillBox);
					toggleNextButtonContainer('hide');
				} else {
					// Insert More Info button if the placeholder is present and hasn't been added already
					var moreButton = part.querySelector('.more-info-button');
					var placeholder = part.querySelector('.more-info-button-placeholder');
					var textAreaField = part.querySelector('.ta-question');

					if (placeholder && moreButton == null) {
						var moreInfoButton = createMoreInfoButton();

						placeholder.parentNode.insertBefore(moreInfoButton, placeholder);
						placeholder.style.display = 'none';
					} else if (textAreaField && moreButton == null) {
						var label = textAreaField.querySelector('.acf-label');
						var moreInfoButton = createMoreInfoButton();
						label.parentNode.insertBefore(moreInfoButton, label.nextSibling);

						// Add a Skip button on the textarea if the class is present
						if (textAreaField.classList.contains('show-skip-button')) {
							var skipButton = createSkipTextareaButton();
							textAreaField.insertAdjacentElement('afterend', skipButton);
						}
					}

					// Validate this form to control visibility of Next button
					checkValidation(part);
				}
			} else {
				part.style.display = 'none';
			}

			// Add event listeners to text inputs
			var requiredElements = part.querySelectorAll('input[required], textarea[required]');

			requiredElements.forEach(function (element) {
				element.addEventListener('input', function () {
					if (areInputFieldsValid(part)) {
						toggleNextButtonContainer('show');
					} else {
						toggleNextButtonContainer('hide'); // Hide if not all fields are valid
					}
				});
			});

			// Intercept the next button if the class exists
			if (part.classList.contains('has-overlay')) hasOverlaysIndexes.push(i);

			// If it has multiple answers, hide the moved elements that aren't part of a hidden form
			var hasMultipleAnswers = part.classList.contains('has_multiple_answers');

			if (hasMultipleAnswers && i === currentIndex) {
				var innerFormParts = part.querySelectorAll('#wills-form #form-part-inner');

				if (innerFormParts) {
					innerFormParts.forEach(function (innerpart, idx) {
						if (innerpart.getAttribute('hidden') === null) {
							var shownInnerFormIndex = 100 + idx;
							var innerMovedElements = document.querySelectorAll('.innerMovedEl');

							innerMovedElements.forEach(function (element) {
								// Extract the index number from the element's ID
								var idIndex = parseInt(element.id.split('-').pop(), 10);

								if (idIndex === shownInnerFormIndex) {
									element.style.display = 'block';

									// Validate this form to control visibility of Next button
									checkValidation(innerpart);
								} else {
									element.style.display = 'none';
								}
							});
						}
					});
				}
			} else if (hasMultipleAnswers && i !== currentIndex) {
				setInnerMovedElementsVisibility('none');
			} else {
				updateMovedElementsVisibility(index);
			}
		});
	}

	function createUrgentWillHTML(urgentWillBox) {
		const copy = `
        <p class="urgent-will-copy">If your Will is urgent, we want to help you as quickly as possible.</p> 
        <p class="urgent-will-copy">Please choose one of the following ways to get in touch with us, and your nearest solicitor will contact you within 2 working days.</p>`;

		const callButtonDesktop = `
        <div class="urgent-will-box">
						<div style="height:36px;">
							<svg xmlns="http://www.w3.org/2000/svg" width="25" height="24" viewBox="0 0 25 24" fill="none">
						<path d="M18.5868 23.8636C20.7831 23.8636 22.2766 23.2635 23.632 21.859C23.7199 21.763 23.8077 21.655 23.8956 21.5589C24.636 20.7667 25 19.9384 25 19.1702C25 18.2699 24.498 17.4537 23.381 16.7214L19.6411 14.2247C18.4613 13.4444 17.0934 13.3844 16.0392 14.3927L15.1104 15.281C14.7841 15.5931 14.4704 15.6171 14.0939 15.365C13.4162 14.9329 12.136 13.8526 11.2575 13.0123C10.3665 12.16 9.46285 11.1997 8.97339 10.4675C8.70984 10.0954 8.73494 9.80731 9.06124 9.49522L9.98996 8.60695C11.0442 7.59864 10.9814 6.29023 10.1531 5.16189L7.49247 1.52477C6.75201 0.492452 5.86094 0.0123038 4.91968 0.000300148C4.11647 -0.0117035 3.26305 0.336403 2.43474 1.03262C2.33434 1.11664 2.23394 1.20067 2.13353 1.28469C0.64006 2.5931 0 3.99753 0 6.1822C0 9.72329 2.14608 13.9006 6.3253 17.8738C10.4794 21.823 14.7967 23.8636 18.5868 23.8636ZM18.5868 21.859C15.3865 21.907 11.4081 19.7344 7.91918 16.4213C4.40512 13.1083 2.07078 9.15912 2.12098 6.09817C2.14608 4.81378 2.62299 3.68543 3.56426 2.89319C3.66466 2.82117 3.73996 2.76115 3.84036 2.68913C4.19177 2.40104 4.56827 2.24499 4.91968 2.24499C5.28363 2.24499 5.60994 2.38903 5.86094 2.74915L8.20783 6.12218C8.50904 6.53031 8.52159 6.99845 8.08233 7.40658L7.07831 8.35487C6.1245 9.24314 6.21235 10.2875 6.81476 11.1037C7.49247 12.004 8.76004 13.3844 9.82681 14.3927C10.9689 15.4731 12.5251 16.8055 13.4287 17.4297C14.2821 18.0058 15.374 18.0899 16.3027 17.1776L17.2942 16.2173C17.7083 15.7972 18.1978 15.8092 18.637 16.0852L22.1009 18.2939C22.49 18.534 22.628 18.8341 22.628 19.1822C22.628 19.5183 22.4649 19.8784 22.1637 20.2265C22.0884 20.3105 22.0256 20.3826 21.9503 20.4786C21.122 21.3909 19.9423 21.847 18.5868 21.859Z" fill="#CC4877"/>
						</svg>
						</div>
            <p class="urgent-will-title">CALL</p>
            <p class="urgent-will-text urgent-will-space"><a class="urgent-link" href="tel:+441749467121">01749 467121</a></p>
            <div style="height:36px;margin-top:2px;"><svg xmlns="http://www.w3.org/2000/svg" width="25" height="21" viewBox="0 0 25 21" fill="none">
						<path d="M3.66044 20.1923H21.6066C23.7539 20.1923 25 18.9051 25 16.4458V3.74655C25 1.28716 23.7428 0 21.3396 0H3.39341C1.24611 0 0 1.27567 0 3.74655V16.4458C0 18.9051 1.25723 20.1923 3.66044 20.1923ZM11.2483 10.4926L3.14864 2.21805C3.30441 2.18357 3.48242 2.16059 3.67156 2.16059H21.3284C21.5287 2.16059 21.7067 2.18357 21.8736 2.22954L13.785 10.4926C13.3178 10.9638 12.9283 11.1707 12.5167 11.1707C12.105 11.1707 11.7156 10.9523 11.2483 10.4926ZM2.08055 3.90745L8.13307 10.0559L2.08055 16.2389V3.90745ZM16.9003 10.0559L22.9194 3.94192V16.2159L16.9003 10.0559ZM3.67156 18.0202C3.47129 18.0202 3.28215 17.9972 3.11526 17.9628L9.49043 11.435L10.0912 12.0556C10.9034 12.8716 11.6822 13.2164 12.5167 13.2164C13.34 13.2164 14.13 12.8716 14.931 12.0556L15.5429 11.435L21.907 17.9513C21.729 17.9972 21.5398 18.0202 21.3284 18.0202H3.67156Z" fill="#CC4877"/>
					</svg></div>
            <p class="urgent-will-title">EMAIL</p>
            <p class="urgent-will-text"><a class="urgent-link" href="mailto:info@lyonsbowe.co.uk">info@lyonsbowe.co.uk</a></p>
        </div>`;

		const contactButtonDesktop = `
				<a href="/online-wills-contact/" class="urgent-will-box-link">
					<div class="urgent-will-box-inner">
							<div style="height:36px;"><svg xmlns="http://www.w3.org/2000/svg" width="25" height="24" viewBox="0 0 25 24" fill="none">
							<path d="M7.13318 23.8149C7.77652 23.8149 8.25056 23.4989 9.02935 22.8104L12.9007 19.4018H19.6953C23.0926 19.4018 25 17.4379 25 14.0858V5.31603C25 1.96388 23.0926 0 19.6953 0H5.30474C1.91874 0 0 1.96388 0 5.31603V14.0858C0 17.4492 1.97517 19.4018 5.22573 19.4018H5.68849V22.167C5.68849 23.1716 6.21896 23.8149 7.13318 23.8149ZM7.69752 21.2867V18.07C7.69752 17.4041 7.40406 17.1445 6.77201 17.1445H5.38375C3.26185 17.1445 2.24605 16.0722 2.24605 14.0181V5.38375C2.24605 3.32957 3.26185 2.25734 5.38375 2.25734H19.6275C21.7381 2.25734 22.754 3.32957 22.754 5.38375V14.0181C22.754 16.0722 21.7381 17.1445 19.6275 17.1445H12.7765C12.088 17.1445 11.7494 17.2573 11.2754 17.7539L7.69752 21.2867ZM6.20768 9.42438H10.9368C11.7946 9.42438 12.2122 9.01806 12.2122 8.16027V6.04966C12.2122 5.19187 11.7946 4.79684 10.9368 4.79684H6.20768C5.34989 4.79684 4.92099 5.19187 4.92099 6.04966V8.16027C4.92099 9.01806 5.34989 9.42438 6.20768 9.42438ZM15.3386 13.8826H18.702C19.5598 13.8826 19.9774 13.4763 19.9774 12.6185V6.81716C19.9774 5.95937 19.5598 5.55305 18.702 5.55305H15.3386C14.4808 5.55305 14.0632 5.95937 14.0632 6.81716V12.6185C14.0632 13.4763 14.4808 13.8826 15.3386 13.8826ZM7.48307 14.5711H11.5688C12.4266 14.5711 12.8442 14.1648 12.8442 13.307V11.8962C12.8442 11.0384 12.4266 10.6208 11.5688 10.6208H7.48307C6.62528 10.6208 6.19639 11.0384 6.19639 11.8962V13.307C6.19639 14.1648 6.62528 14.5711 7.48307 14.5711Z" fill="#CC4877"/>
						</svg></div>
							<p class="urgent-will-title" style="line-height:30px;">CONTACT<br>FORM</p>
					</div>
				</a>`;

		const callButtonMobile = `
				<a href="tel:+441749467121" class="urgent-will-mobile-link">
					<div class="urgent-will-box-inner-mobile">
							<span style="height:34px;"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="21" viewBox="0 0 25 24" fill="none">
							<path d="M18.5868 23.8636C20.7831 23.8636 22.2766 23.2635 23.632 21.859C23.7199 21.763 23.8077 21.655 23.8956 21.5589C24.636 20.7667 25 19.9384 25 19.1702C25 18.2699 24.498 17.4537 23.381 16.7214L19.6411 14.2247C18.4613 13.4444 17.0934 13.3844 16.0392 14.3927L15.1104 15.281C14.7841 15.5931 14.4704 15.6171 14.0939 15.365C13.4162 14.9329 12.136 13.8526 11.2575 13.0123C10.3665 12.16 9.46285 11.1997 8.97339 10.4675C8.70984 10.0954 8.73494 9.80731 9.06124 9.49522L9.98996 8.60695C11.0442 7.59864 10.9814 6.29023 10.1531 5.16189L7.49247 1.52477C6.75201 0.492452 5.86094 0.0123038 4.91968 0.000300148C4.11647 -0.0117035 3.26305 0.336403 2.43474 1.03262C2.33434 1.11664 2.23394 1.20067 2.13353 1.28469C0.64006 2.5931 0 3.99753 0 6.1822C0 9.72329 2.14608 13.9006 6.3253 17.8738C10.4794 21.823 14.7967 23.8636 18.5868 23.8636ZM18.5868 21.859C15.3865 21.907 11.4081 19.7344 7.91918 16.4213C4.40512 13.1083 2.07078 9.15912 2.12098 6.09817C2.14608 4.81378 2.62299 3.68543 3.56426 2.89319C3.66466 2.82117 3.73996 2.76115 3.84036 2.68913C4.19177 2.40104 4.56827 2.24499 4.91968 2.24499C5.28363 2.24499 5.60994 2.38903 5.86094 2.74915L8.20783 6.12218C8.50904 6.53031 8.52159 6.99845 8.08233 7.40658L7.07831 8.35487C6.1245 9.24314 6.21235 10.2875 6.81476 11.1037C7.49247 12.004 8.76004 13.3844 9.82681 14.3927C10.9689 15.4731 12.5251 16.8055 13.4287 17.4297C14.2821 18.0058 15.374 18.0899 16.3027 17.1776L17.2942 16.2173C17.7083 15.7972 18.1978 15.8092 18.637 16.0852L22.1009 18.2939C22.49 18.534 22.628 18.8341 22.628 19.1822C22.628 19.5183 22.4649 19.8784 22.1637 20.2265C22.0884 20.3105 22.0256 20.3826 21.9503 20.4786C21.122 21.3909 19.9423 21.847 18.5868 21.859Z" fill="#CC4877"/>
							</svg></span>
							<p class="urgent-will-title" style="line-height:30px;padding-left:15px;">CALL</p>
					</div>
				</a>`;

		const emailButtonMobile = `
				<a href="mailto:info@lyonsbowe.co.uk" class="urgent-will-mobile-link">
					<div class="urgent-will-box-inner-mobile">
							<span style="height:33px;"><svg xmlns="http://www.w3.org/2000/svg" width="23" height="22" viewBox="0 0 25 24" fill="none">
							<path d="M3.66044 20.1923H21.6066C23.7539 20.1923 25 18.9051 25 16.4458V3.74655C25 1.28716 23.7428 0 21.3396 0H3.39341C1.24611 0 0 1.27567 0 3.74655V16.4458C0 18.9051 1.25723 20.1923 3.66044 20.1923ZM11.2483 10.4926L3.14864 2.21805C3.30441 2.18357 3.48242 2.16059 3.67156 2.16059H21.3284C21.5287 2.16059 21.7067 2.18357 21.8736 2.22954L13.785 10.4926C13.3178 10.9638 12.9283 11.1707 12.5167 11.1707C12.105 11.1707 11.7156 10.9523 11.2483 10.4926ZM2.08055 3.90745L8.13307 10.0559L2.08055 16.2389V3.90745ZM16.9003 10.0559L22.9194 3.94192V16.2159L16.9003 10.0559ZM3.67156 18.0202C3.47129 18.0202 3.28215 17.9972 3.11526 17.9628L9.49043 11.435L10.0912 12.0556C10.9034 12.8716 11.6822 13.2164 12.5167 13.2164C13.34 13.2164 14.13 12.8716 14.931 12.0556L15.5429 11.435L21.907 17.9513C21.729 17.9972 21.5398 18.0202 21.3284 18.0202H3.67156Z" fill="#CC4877"/>
						</svg></span>
							<p class="urgent-will-title" style="line-height:30px;padding-left:15px;">EMAIL</p>
					</div>
				</a>`;

		const contactButtonMobile = `
				<a href="/online-wills-contact/" class="urgent-will-mobile-link">
					<div class="urgent-will-box-inner-mobile">
							<span style="height:33px;"><svg xmlns="http://www.w3.org/2000/svg" width="23" height="22" viewBox="0 0 25 24" fill="none">
							<path d="M7.13318 23.8149C7.77652 23.8149 8.25056 23.4989 9.02935 22.8104L12.9007 19.4018H19.6953C23.0926 19.4018 25 17.4379 25 14.0858V5.31603C25 1.96388 23.0926 0 19.6953 0H5.30474C1.91874 0 0 1.96388 0 5.31603V14.0858C0 17.4492 1.97517 19.4018 5.22573 19.4018H5.68849V22.167C5.68849 23.1716 6.21896 23.8149 7.13318 23.8149ZM7.69752 21.2867V18.07C7.69752 17.4041 7.40406 17.1445 6.77201 17.1445H5.38375C3.26185 17.1445 2.24605 16.0722 2.24605 14.0181V5.38375C2.24605 3.32957 3.26185 2.25734 5.38375 2.25734H19.6275C21.7381 2.25734 22.754 3.32957 22.754 5.38375V14.0181C22.754 16.0722 21.7381 17.1445 19.6275 17.1445H12.7765C12.088 17.1445 11.7494 17.2573 11.2754 17.7539L7.69752 21.2867ZM6.20768 9.42438H10.9368C11.7946 9.42438 12.2122 9.01806 12.2122 8.16027V6.04966C12.2122 5.19187 11.7946 4.79684 10.9368 4.79684H6.20768C5.34989 4.79684 4.92099 5.19187 4.92099 6.04966V8.16027C4.92099 9.01806 5.34989 9.42438 6.20768 9.42438ZM15.3386 13.8826H18.702C19.5598 13.8826 19.9774 13.4763 19.9774 12.6185V6.81716C19.9774 5.95937 19.5598 5.55305 18.702 5.55305H15.3386C14.4808 5.55305 14.0632 5.95937 14.0632 6.81716V12.6185C14.0632 13.4763 14.4808 13.8826 15.3386 13.8826ZM7.48307 14.5711H11.5688C12.4266 14.5711 12.8442 14.1648 12.8442 13.307V11.8962C12.8442 11.0384 12.4266 10.6208 11.5688 10.6208H7.48307C6.62528 10.6208 6.19639 11.0384 6.19639 11.8962V13.307C6.19639 14.1648 6.62528 14.5711 7.48307 14.5711Z" fill="#CC4877"/>
						</svg></span>
							<p class="urgent-will-title" style="line-height:30px;padding-left:15px;">FORM</p>
					</div>
				</a>`;

		const buttonsBox = `
				<div class="urgent-will-buttons">${callButtonDesktop}${contactButtonDesktop}${callButtonMobile}${emailButtonMobile}${contactButtonMobile}</div>
		`;

		// Append new content
		urgentWillBox.innerHTML = copy + buttonsBox;
	}

	// Toggle the visibility of the next button container
	function toggleNextButtonContainer(display) {
		// Hide the submit button until last form
		showSubmitButton();

		// React if the form has an overlay
		if (hasOverlaysIndexes.includes(currentIndex) && !overlayShown) {
			handleShowingOverlay();
		}

		if (display === 'hide') {
			nextButtonContainer.style.display = 'none';
		} else {
			if (currentIndex === formParts.length - 1) {
				nextButtonContainer.style.display = 'none';

				// Last form - show the submit button
				showSubmitButton('show');
			} else {
				nextButtonContainer.style.display = 'flex';
			}
		}
	}

	// Handle showing the form overlay and buttons
	function handleShowingOverlay() {
		var formContainer = document.querySelector('#form-container');
		var overlayContainer = document.querySelector('#overlay-container');
		var onlinewillContainer = document.querySelector('#onlinewill-container');
		var onlinewillImage = document.querySelector('#onlinewill-image');
		var inpersonImage = document.querySelector('#inpersonwill-image');
		var inpersonGetStarted = document.querySelector('#inpersonGetStarted');
		var onlinewillGetStarted = document.querySelector('#onlinewillGetStarted');

		//console.log('---INSERT OVERLAY---', screen.width);

		if (formContainer && overlayContainer) {
			// Is isOnlineWillValid is false, we need to hide the online will option and change the images
			if (screen.width >= 1000) {
				if (!validateTestFields()) {
					onlinewillContainer.style.display = 'none';
					onlinewillImage.style.display = 'none';
					inpersonImage.style.display = 'flex';
				} else {
					onlinewillImage.style.display = 'flex';
					inpersonImage.style.display = 'none';
				}
			}

			// Add click event to overlay buttons
			inpersonGetStarted.addEventListener('click', function (event) {
				event.preventDefault();
				handleHidingOverlay();
			});
			onlinewillGetStarted.addEventListener('click', function (event) {
				event.preventDefault();
				handleHidingOverlay();
			});

			// Hide the form and show the overlay
			formContainer.style.display = 'none';
			overlayContainer.style.display = 'flex';

			// Prevent overlay showing again
			overlayShown = true;
		}
	}

	// Hide the overlay
	function handleHidingOverlay() {
		var formContainer = document.querySelector('#form-container');
		var overlayContainer = document.querySelector('#overlay-container');

		// Hide the form and show the overlay
		formContainer.style.display = 'flex';
		overlayContainer.style.display = 'none';
	}

	// Validate fields with ID test_field-yes/no to determine which parts of the overlay to show
	function validateTestFields() {
		let isOnlineWillValid = true;

		// Select all wrapper elements with IDs starting with 'test_'
		let testElements = document.querySelectorAll('[id^="test_"]');

		testElements.forEach(wrapper => {
			// Extract the expected value from the ID
			let expectedValue = wrapper.id.split('-')[1];

			// Find the relevant input or control within the wrapper
			let input = wrapper.querySelector(
				'input[type="checkbox"], input[type="radio"], input[type="text"], select',
			);

			let currentValue = '';

			if (input) {
				// Determine the current value based on element type
				switch (input.type) {
					case 'checkbox':
						currentValue = input.checked ? 'yes' : 'no';
						break;
					case 'radio':
						// Find the checked radio button in the group
						let checkedRadio = wrapper.querySelector('input[type="radio"]:checked');
						currentValue = checkedRadio ? checkedRadio.value : '';
						break;
					case 'text':
					case 'select':
						currentValue = input.value;
						break;
					default:
						//console.error('Unhandled input type:', input.type);
						isOnlineWillValid = false;
				}
			} else {
				isOnlineWillValid = false;
			}

			// Validate the current value against the expected value
			if (currentValue === expectedValue) {
				isOnlineWillValid = false;
			}
		});
		return isOnlineWillValid;
	}

	// Handle visibility of submit button if we're on the last form
	function showSubmitButton(display) {
		var submitButton = document.querySelector('.fea-submit-button');

		if (submitButton && display === 'show') {
			submitButton.style.visibility = 'visible';
			submitButton.style.display = 'block';
		} else if (submitButton) {
			submitButton.style.display = 'none';
		}
	}

	// Move section title, help title and help text
	function moveElements(elArray, elContainer, className, index, isInner, addClass = true) {
		var lastIndex = -1;

		elArray.forEach(function (el) {
			if (lastIndex !== index) {
				if (elContainer) {
					elContainer.appendChild(el);
					el.id = `${className + '-' + index}`;
					el.classList.add(isInner ? 'innerMovedEl' : 'movedEl');
					if (addClass) el.classList.add(className);
					lastIndex = index;
				}
			}
		});
	}

	// Update the visibility of moved elements
	function updateMovedElementsVisibility(currentIndex, hasMultipleAnswers = false) {
		var movedElements = document.querySelectorAll('.movedEl');

		movedElements.forEach(function (element) {
			// Extract the index number from the element's ID
			var index = parseInt(element.id.split('-').pop(), 10);

			// Show the element if the index matches, otherwise hide it
			element.style.display = index === currentIndex ? 'block' : 'none';
		});

		if (hasMultipleAnswers) {
			// Hide inner moved elements
			setInnerMovedElementsVisibility('none');
		}
	}

	// Control visibility of moveable inner form parts
	function setInnerMovedElementsVisibility(visibility) {
		var innerMovedElements = document.querySelectorAll('.innerMovedEl');

		innerMovedElements.forEach(function (element) {
			element.style.display = visibility;
		});
	}

	// Add a key listener to a textarea component
	function addTextareaKeyListener() {
		// Select all text area elements with the class 'ta-question'
		var textAreas = document.querySelectorAll('.ta-question  textarea');

		textAreas.forEach(function (textArea) {
			textArea.addEventListener('input', function () {
				// Check if the number of characters entered meets the minimum requirement
				if (textArea && textArea.value.length >= minKeyCount) {
					toggleNextButtonContainer('show');
				} else {
					toggleNextButtonContainer('hide');
				}
			});
		});
	}

	/****************************
	 *        VALIDATION        *
	 *****************************/

	// Validate the form part
	function checkValidation(formPart) {
		var isTrueFalseValid = areTrueFalseFieldsValid(formPart);
		var isCustomSelectValid = areCustomSelectFieldsValid(formPart);
		var isRequiredFieldsValid = areAllRequiredFieldsValid(
			formPart.querySelectorAll(
				'.acf-input-wrap > input[required], .acf-input-wrap > textarea[required]',
			),
		);
		var isTextareaValid = areTextareaFieldsValid(formPart);
		var isRadioGroupValid = areCustomRadioFieldsValid(formPart);

		if (
			isTrueFalseValid &&
			isCustomSelectValid &&
			isRequiredFieldsValid &&
			isTextareaValid &&
			isRadioGroupValid
		) {
			toggleNextButtonContainer('show');
		} else {
			toggleNextButtonContainer('hide');
		}
	}

	// Validate 'required' form fields
	function areAllRequiredFieldsValid(requiredFields) {
		if (requiredFields) {
			for (var element of requiredFields) {
				// Basic validation for non-empty and field validity
				if (!element.value.trim() || !element.checkValidity()) {
					return false;
				}

				// Additional validation for email fields
				if (element.type === 'email' && !isValidEmail(element.value)) {
					return false;
				}
			}
			return true;
		}
		return false;
	}

	// Helper function to validate email using a regular expression
	function isValidEmail(email) {
		var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
		return emailRegex.test(email);
	}

	// Validate 'required' text inputs
	function areInputFieldsValid(formPart) {
		var requiredFields = formPart.querySelectorAll('input[required], textarea[required]');
		return areAllRequiredFieldsValid(requiredFields);
	}

	function areTrueFalseFieldsValid(formPart) {
		var trueFalseFields = formPart.querySelectorAll('.acf-true-false');

		if (trueFalseFields) {
			for (var field of trueFalseFields) {
				var yesButton = field.querySelector('.yes-button');
				var noButton = field.querySelector('.no-button');

				// Check if either 'Yes' or 'No' button is active
				if (!yesButton.classList.contains('active') && !noButton.classList.contains('active')) {
					return false;
				}
			}
			return true;
		}
		return false;
	}

	function areCustomSelectFieldsValid(formPart) {
		var customSelects = formPart.querySelectorAll('.custom-select');

		if (customSelects) {
			for (var customSelect of customSelects) {
				var currentDiv = customSelect.querySelector('.current, .no-option');

				// Check if the current option is the placeholder
				if (!currentDiv || currentDiv.textContent === 'Choose from dropdown') {
					return false;
				}
			}
			return true;
		}
		return false;
	}

	function areTextareaFieldsValid(formPart) {
		// Select text area
		var textArea = formPart.querySelector('.ta-question  textarea');

		if (textArea) {
			if (textArea.value.length >= minKeyCount) {
				return true;
			}
			return false;
		}
		return true;
	}

	function areCustomRadioFieldsValid(formPart) {
		var customRadioGroups = formPart.querySelectorAll('.custom-radio-group');

		// Iterate over each group of custom radio buttons
		for (var customRadioGroup of customRadioGroups) {
			var isSelected = false;

			// Check each custom radio button in the group
			var customRadios = customRadioGroup.querySelectorAll('.custom-radio');
			for (var customRadio of customRadios) {
				// If any radio button is selected, set isSelected to true
				if (customRadio.classList.contains('selected')) {
					isSelected = true;
					break; // Stop checking more radios since we found a selected one
				}
			}

			// If no radio button is selected in the current group, return false
			if (!isSelected) {
				return false;
			}
		}

		// If all groups have a selected radio button, return true
		return true;
	}

	/****************************
	  CUSTOM MORE INFO BUTTON 
	*****************************/

	function createMoreInfoButton() {
		var moreInfoButton = document.createElement('button');
		moreInfoButton.type = 'button';
		moreInfoButton.innerText = 'MORE INFO';
		moreInfoButton.className = 'more-info-button';

		moreInfoButton.addEventListener('click', function () {
			toggleHelpBoxVisibility();
		});

		return moreInfoButton;
	}

	function toggleHelpBoxVisibility() {
		var helpBox = document.querySelector('#help-box');

		if (helpBox.style.display === '') {
			helpBox.style.display = 'none';
		}
		helpBox.style.display = helpBox.style.display === 'none' ? 'flex' : 'none';
	}

	/****************************
	  CUSTOM SKIP TEXTAREA BUTTON 
	*****************************/

	function createSkipTextareaButton() {
		var skipButton = document.createElement('button');
		skipButton.type = 'button';
		skipButton.innerText = 'SKIP';
		skipButton.className = 'skip-button';

		// Override the minKeyCount test and show the next button
		skipButton.addEventListener('click', function () {
			toggleNextButtonContainer('show');
		});

		return skipButton;
	}

	/****************************
	  CUSTOM RADIO BUTTONS 
	*****************************/

	function replaceRadioButtons() {
		// Find all radio button groups
		const radioGroups = document.querySelectorAll('.acf-field-radio');

		radioGroups.forEach(group => {
			const radioInputs = group.querySelectorAll('input[type="radio"]');
			const customRadioGroup = document.createElement('div');
			customRadioGroup.className = 'custom-radio-group';

			radioInputs.forEach(radioInput => {
				const customRadio = document.createElement('div');
				customRadio.className = 'custom-radio';
				customRadio.setAttribute('data-value', radioInput.value);

				const label = document.createElement('span');
				label.textContent = radioInput.value;

				customRadio.appendChild(label);
				customRadioGroup.appendChild(customRadio);

				// Hide original radio inputs
				radioInput.style.display = 'none';

				customRadio.addEventListener('click', () => {
					// Reset all custom radios in this group
					customRadioGroup.querySelectorAll('.custom-radio').forEach(otherCustomRadio => {
						otherCustomRadio.classList.remove('selected');
					});

					// Mark the clicked one as selected
					customRadio.classList.add('selected');

					// Check the actual radio button which will hold the value for form submission
					radioInput.checked = true;

					// IMPORTANT: Manually trigger the change event on the original radio
					const event = new Event('change', {bubbles: true});
					radioInput.dispatchEvent(event);

					// Show the next button container
					toggleNextButtonContainer('show');
				});
			});

			// Insert custom radios after the label of the radio group
			const label = group.querySelector('.acf-label');
			if (label) {
				label.parentNode.insertBefore(customRadioGroup, label.nextSibling);
			}

			var moreInfoButton = createMoreInfoButton();
			customRadioGroup.parentNode.insertBefore(moreInfoButton, customRadioGroup);
		});
	}

	/****************************
	  CUSTOM TRUE/FALSE BUTTONS 
	*****************************/

	function replaceTrueFalseFields(trueFalseFields) {
		trueFalseFields.forEach(function (field) {
			var yesButton = document.createElement('button');
			yesButton.type = 'button';
			yesButton.innerText = 'Yes';
			yesButton.className = 'toggle-button yes-button';

			var noButton = document.createElement('button');
			noButton.type = 'button';
			noButton.innerText = 'No';
			noButton.className = 'toggle-button no-button';

			var moreInfoButton = createMoreInfoButton();
			field.appendChild(moreInfoButton);

			field.appendChild(yesButton);
			field.appendChild(noButton);

			var inputHidden = field.querySelector('input[type="hidden"]');
			var checkbox = field.querySelector('input[type="checkbox"]');

			yesButton.addEventListener('click', function () {
				checkbox.checked = true;
				inputHidden.value = '1';
				yesButton.classList.add('active');
				noButton.classList.remove('active');
				toggleNextButtonContainer('show');
			});

			noButton.addEventListener('click', function () {
				checkbox.checked = false;
				inputHidden.value = '0';
				yesButton.classList.remove('active');
				noButton.classList.add('active');
				toggleNextButtonContainer('show');
			});
		});
	}

	/****************************
	  CUSTOM SELECT OPTION LIST 
	*****************************/

	function replaceSelectFields(selects) {
		selects.forEach(function (select) {
			// Create a new div that will act as the custom dropdown
			var customSelect = document.createElement('div');
			customSelect.className = 'custom-select';

			// This div will show the selected option
			var current = document.createElement('div');
			var currentOptionText = select.options[select.selectedIndex].text;

			if (currentOptionText.includes('Select')) {
				current.className = 'no-option';
				current.textContent = 'Choose from dropdown';
			} else {
				current.className = 'current';
				current.textContent = currentOptionText;
			}

			customSelect.appendChild(current);

			// The list of options
			var optionsList = document.createElement('div');
			optionsList.className = 'options-container';
			optionsList.style.display = 'none';

			// Use Countries if the ID matches 'countries'
			/* code uses country list her not in ACF field - can remove
			var parentWithCountriesId = select.closest('#countries');
			var isCountries = parentWithCountriesId !== null;
			var selectOptions = isCountries ? getCountryList() : select.options;
			*/
			var selectOptions = select.options;

			// Populate the options
			Array.from(selectOptions).forEach(function (optionEl) {
				var option = document.createElement('div');
				var optionText = optionEl.text;

				if (!optionText.includes('Select')) {
					option.className = 'option';

					// Test the string for the tag [FWS] which is used to find an option that needs a tag in the ACF fields
					if (optionText.includes('[FWS]')) {
						// Revove the test string
						option.textContent = optionText.replace(/ \[FWS\]$/, '');

						// Create and add the tag
						var tagSpan = document.createElement('span');
						tagSpan.className = 'option-tag';
						tagSpan.textContent = 'FWS';

						// Create and add the circle element
						var circle = document.createElement('span');
						circle.className = 'tag-circle';
						tagSpan.prepend(circle);

						option.appendChild(tagSpan);
					} else {
						option.textContent = optionText;
					}
				}

				option.setAttribute('data-value', optionEl.value.replace(/ \[FWS\]$/, ''));

				option.addEventListener('click', function () {
					// Update the original select's value
					var optionVal = option.getAttribute('data-value');
					//console.log('optionVal', optionVal);

					select.value = optionVal.replace(/ \[FWS\]$/, '');

					// Hide the custom options list
					optionsList.style.display = 'none';

					// Update the visual representation of the current selected option
					var displayText = option.textContent;
					if (displayText.includes('FWS')) {
						displayText = displayText.replace('FWS', '').trim(); // Remove 'FWS' and trim any extra whitespace
					}
					current.textContent = displayText;

					// Adjust the classes for open/closed state
					customSelect.classList.remove('open');
					customSelect.classList.add('closed');

					// IMPORTANT: Manually trigger the change event on the original select
					var event = new Event('change', {bubbles: true});
					select.dispatchEvent(event);

					// Show the Next button
					toggleNextButtonContainer('show');
				});

				optionsList.appendChild(option);
			});

			customSelect.appendChild(optionsList);

			// Add the closed class initially
			customSelect.classList.add('closed');

			// Toggle the options list when the current value is clicked
			current.addEventListener('click', function () {
				var isClosed = optionsList.style.display === 'none';

				optionsList.style.display = isClosed ? 'block' : 'none';

				// Change the opacity of the selected item text to 1
				current.classList.add('selected-option');

				if (isClosed) {
					customSelect.classList.add('open');
					customSelect.classList.remove('closed');
				} else {
					customSelect.classList.remove('open');
					customSelect.classList.add('closed');
				}
			});

			// Hide the original select and place the custom one before it in the DOM
			select.style.display = 'none';
			select.parentNode.insertBefore(customSelect, select);

			var moreInfoButton = createMoreInfoButton();
			customSelect.parentNode.insertBefore(moreInfoButton, customSelect);
		});
	}

	/*
	// Get the list of countries for the countries dropdown
	function getCountryList() {
		const rawHTML = `
		<select class="form-select" id="country" name="country">
			<option value="AX">Aland Islands</option>
			<option value="AL">Albania</option>
			<option value="AD">Andorra</option>
			<option value="AT">Austria</option>
			<option value="BY">Belarus</option>
			<option value="BE">Belgium</option>
			<option value="BA">Bosnia and Herzegovina</option>
			<option value="BG">Bulgaria</option>
			<option value="HR">Croatia</option>
			<option value="CZ">Czech Republic</option>
			<option value="DK">Denmark</option>
			<option value="EE">Estonia</option>
			<option value="FO">Faroe Islands</option>
			<option value="FI">Finland</option>
			<option value="FR">France</option>
			<option value="DE">Germany</option>
			<option value="GI">Gibraltar</option>
			<option value="GR">Greece</option>
			<option value="GG">Guernsey</option>
			<option value="VA">Holy See (Vatican City State)</option>
			<option value="HU">Hungary</option>
			<option value="IS">Iceland</option>
			<option value="IE">Ireland</option>
			<option value="IM">Isle of Man</option>
			<option value="IT">Italy</option>
			<option value="JE">Jersey</option>
			<option value="XK">Kosovo</option>
			<option value="LV">Latvia</option>
			<option value="LI">Liechtenstein</option>
			<option value="LT">Lithuania</option>
			<option value="LU">Luxembourg</option>
			<option value="MK">Macedonia, the Former Yugoslav Republic of</option>
			<option value="MT">Malta</option>
			<option value="MD">Moldova, Republic of</option>
			<option value="MC">Monaco</option>
			<option value="ME">Montenegro</option>
			<option value="NL">Netherlands</option>
			<option value="NO">Norway</option>
			<option value="PL">Poland</option>
			<option value="PT">Portugal</option>
			<option value="RO">Romania</option>
			<option value="SM">San Marino</option>
			<option value="RS">Serbia</option>
			<option value="CS">Serbia and Montenegro</option>
			<option value="SK">Slovakia</option>
			<option value="SI">Slovenia</option>
			<option value="ES">Spain</option>
			<option value="SJ">Svalbard and Jan Mayen</option>
			<option value="SE">Sweden</option>
			<option value="CH">Switzerland</option>
			<option value="UA">Ukraine</option>
			<option value="GB">United Kingdom</option>
			<option value="not-listed">Country Not Listed</option>
		</select>`;

		// Parse the HTML
		const parser = new DOMParser();
		const doc = parser.parseFromString(rawHTML, 'text/html');

		// Extract the options and convert to array
		const options = doc.querySelectorAll('option');
		const countryOptions = Array.from(options).map(option => {
			return {value: option.value, text: option.textContent};
		});

		return countryOptions;
	}
	*/

	/*****************************
	 *     INITIALISATION        *
	 *****************************/

	// Replace each radio button with a custom design
	replaceRadioButtons();

	// Replace each select element with a custom dropdown
	replaceSelectFields(document.querySelectorAll('#wills-form select'));

	// Replace each true/false element with custom buttons
	replaceTrueFalseFields(document.querySelectorAll('.acf-true-false'));

	// Add a key listener to text area components
	addTextareaKeyListener();

	// Initial call to show the help elements for the first part
	updateMovedElementsVisibility(0, true);

	// Hide Next button initially
	toggleNextButtonContainer('hide');
});
