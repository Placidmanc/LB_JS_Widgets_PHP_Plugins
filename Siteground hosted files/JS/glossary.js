jQuery(document).ready(function ($) {
	const minSearchLength = 3;
	let letterClicked = false;
	let isTextSearch = false;

	// Remove active classes
	function removeActiveClasses() {
		$('.letter-button-active').removeClass('letter-button-active');
		$('.g-term-active').removeClass('g-term-active');
	}

	// Add active class to clicked letter button
	function addActiveLetterClass(letter) {
		$('.letter-button[data-letter="' + letter + '"]').addClass('letter-button-active');
	}

	// Update the div attribute to show the selected letter
	function setLetterBackground(letter) {
		$('.g-wrapper-full').attr('data-letter', letter);
	}

	function getGlossaryTerms(letter, search, specificTerm) {
		// Update the search flag for text search
		isTextSearch = search.length > 0 ? true : false;

		// Clear previous results
		$('#term-dept').html('');
		$('#term-title').html('');
		$('#term-definition').html('');
		$('#first-list-related').html('');
		$('#second-list-related').html('');
		$('#first-list').html('');
		$('#second-list').html('');

		// Only clear the search box if another search type has been specified i.e. not search
		if (!isTextSearch) {
			$('#glossary-search').val('');
		}

		// Remove previous active classes
		removeActiveClasses();

		const data = {
			action: 'filter_glossary',
			letter: letter,
			search: search,
			specificTerm: specificTerm,
		};

		// Function to append term data to the glossary-definition
		function appendTermData(term) {
			if (!isTextSearch && term.term_title) {
				$('#term-title').html(term.term_title);
				$('#term-definition').html(term.definition);

				if (term.term_title === 'No results found') {
					$('#term-dept').css('visibility', 'hidden');
				} else {
					$('#term-dept').html(term.department);
					$('#term-dept').css('visibility', 'visible');
				}

				if (term.related_terms[0] === 'No related terms') {
					$('#first-list-related').append('<li>No related terms</li>');
				} else {
					$.each(term.related_terms, function (i, relatedTerm) {
						// Split the result into two, one for the first column and the other for the second column
						let relatedColumnId =
							i < term.related_terms.length / 2 ? '#first-list-related' : '#second-list-related';

						$(relatedColumnId).append(
							'<li><a href="#" class="term-link g-related-term" data-term="' +
								relatedTerm.term_name +
								'">' +
								relatedTerm.term_title +
								'</a></li>',
						);
					});
				}
			}
		}

		$.ajax({
			url: glossary_object.glossary_url,
			data: data,
			type: 'POST',
			dataType: 'json',
			success: function (result) {
				let defined = false;
				let theTerm = {};
				let selectedLetter;

				$.each(result, function (index, term) {
					let isActiveTerm = false;

					// Check if this is the term that should be active
					if (specificTerm === term.term_name) {
						theTerm = term;
						isActiveTerm = true;
						defined = true;
					} else if (!defined && index === 0 && letterClicked) {
						// Check if a letter click occurred
						theTerm = term;
						isActiveTerm = true;
						defined = true;
					}

					// Add the active class if this is the active term
					let termClass = isActiveTerm ? 'term-link g-term g-term-active' : 'term-link g-term';

					// Split the result into two, one for the first column and the other for the second column
					let columnId = index < result.length / 2 ? '#first-list' : '#second-list';

					// Append list items to the correct column
					$(columnId).append(
						'<li><a href="#" class="' +
							termClass +
							'" data-term="' +
							term.term_name +
							'">' +
							term.term_title +
							'</a></li>',
					);
				});
				// Add the selected or default (first) term
				appendTermData(theTerm);

				// Add active class to the selected letter button
				if (letter.length > 0) {
					selectedLetter = letter;
				} else if (!isTextSearch) {
					selectedLetter = theTerm.term_title.charAt(0);
				} else if (isTextSearch) {
					selectedLetter = search.charAt(0).toUpperCase();
				}
				addActiveLetterClass(selectedLetter);

				// Set the letter background
				setLetterBackground(selectedLetter);

				// Reset the letter pressed flag
				letterClicked = false;
			},
			error: function (err) {
				console.log('There was an error ' + err);
			},
		});
	}

	$('.letter-button').on('click', function () {
		const letter = $(this).data('letter');
		letterClicked = true;
		isTextSearch = false;
		getGlossaryTerms(letter, '');
	});

	$('#glossary-search').on('keyup', function () {
		const search = $(this).val();
		if (search.length >= minSearchLength) {
			isTextSearch = true;
			getGlossaryTerms('', search);
		}
	});

	$(document).on('click', '.term-link', function (e) {
		e.preventDefault();
		const specificTerm = $(this).data('term');
		isTextSearch = false;
		getGlossaryTerms('', '', specificTerm);
	});

	// Simulate a click on the first letter button on page load
	$(window).on('load', function () {
		letterClicked = true;
		isTextSearch = false;
		$('.letter-button[data-letter="A"]').click();
	});
});
