jQuery(document).ready(function ($) {
	// Hide all children categories initially
	$('.guide-child-category-list').hide();

	// Get current URL
	var url = window.location.href;

	// Function to sanitize title to match the URL structure
	var sanitizeTitle = function (title) {
		return title
			.toLowerCase()
			.replace(/[\s\/]+/g, '-') // Replace spaces and slashes with -
			.replace(/[()]/g, ''); // Remove parentheses
	};

	// Check if any child category is selected
	var isChildSelected =
		$('.child-category a').filter(function () {
			// Sanitize the category title and compare it to the current URL
			var childCategory = '/' + sanitizeTitle($(this).text()) + '/';
			return url.includes(childCategory);
		}).length > 0;

	if (isChildSelected) {
		// If a child category is selected, show its parent's children and add 'active-guide-category' class to the parent
		var selectedChild = $('.child-category a').filter(function () {
			var childCategory = '/' + sanitizeTitle($(this).text()) + '/';
			return url.includes(childCategory);
		});
		selectedChild
			.closest('.guide-parent-category')
			.find('.guide-child-category-list')
			.show()
			.closest('.guide-parent-category')
			.addClass('active-guide-category');
		selectedChild.addClass('active-guide-category'); // Add class to the selected child category
	} else {
		// If no child category is selected, show the first parent's children and add 'active-guide-category' class to the parent
		$('.guide-parent-category')
			.first()
			.find('.guide-child-category-list')
			.show()
			.closest('.guide-parent-category')
			.addClass('active-guide-category');
	}

	// Hide children of other parents and show children of the clicked parent
	$('.guide-parent-category').click(function () {
		if (!$(this).hasClass('active-guide-category')) {
			$('.guide-parent-category').not(this).find('.guide-child-category-list').hide();
			$(this).find('.guide-child-category-list').show();
			$('.guide-parent-category').removeClass('active-guide-category');
			$(this).addClass('active-guide-category');
		}
	});
});
