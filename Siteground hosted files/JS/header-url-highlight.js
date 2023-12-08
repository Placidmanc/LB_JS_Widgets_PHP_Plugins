window.addEventListener('load', function () {
	const urlFull = window.location.href.split('/');
	const urlPart = urlFull[3];

	// Prevent code running if the page has no header
	if (typeof urlPart === 'string' && urlPart.length !== 0 && !urlPart.includes('online-wills')) {
		const hdr = document.getElementsByClassName('e-n-menu-heading');
		const hdrChildren = hdr[0].children;

		// Remove any previously set active class
		for (let child of hdrChildren) {
			child.classList.remove('hdr-active');
		}

		let activateIndex;
		const indexMapping = {
			'property-law': 0,
			'wills-probate': 1,
			'family-law': 2,
			'our-team': 3,
			careers: 3,
			glossary: 4,
			guides: 4,
			blog: 4,
			contact: 5,
		};

		if (urlPart === 'locations') {
			const urlLocationRoot = urlFull[5];
			const urlLocationChild = urlFull[6];
			if (urlLocationRoot === 'services') {
				activateIndex = indexMapping[urlLocationChild];
			} else {
				activateIndex = indexMapping[urlLocationRoot];
			}
		} else {
			activateIndex =
				indexMapping[urlPart] !== undefined ? indexMapping[urlPart] : indexMapping[urlFull[4]];
		}

		if (activateIndex !== undefined) {
			hdrChildren[activateIndex].classList.add('hdr-active');
		}
	}
});
