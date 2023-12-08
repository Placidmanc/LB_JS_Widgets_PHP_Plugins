jQuery(document).ready(function ($) {
	// Check if '/locations/' is in the current URL
	const currentURL = window.location.href;
	const locationIndex = currentURL.indexOf('/locations/');

	if (locationIndex !== -1) {
		// Extract the location slug
		const endOfLocationSlug = currentURL.indexOf('/', locationIndex + 11);
		const locationPath = currentURL.substring(
			locationIndex,
			endOfLocationSlug === -1 ? undefined : endOfLocationSlug,
		);

		// Update the links with the custom attribute 'data-update-link="true"'
		$('a[data-update-link="true"]').each(function () {
			const elem = $(this);
			if (elem.length > 0) {
				const oldURL = elem.attr('href');
				if (oldURL && oldURL.indexOf('/locations/') === -1) {
					// Construct new URL
					const newURL = `${locationPath}${oldURL}`;
					elem.attr('href', newURL);
				}
			}
		});
	}
});
