document.addEventListener('DOMContentLoaded', function () {
	const adminBar = document.querySelector('#wpadminbar');
	const html = document.querySelector('html');
	const body = document.body;

	/**
	 * Return if condition not met
	 */
	if (adminBar === null) {
		return;
	}

	/**
	 * Add class for compatibility
	 */
	adminBar.classList.add('bottom-admin-toolbar');
	const adminBarHeight = adminBar.clientHeight + 'px';
	adminBar.style.setProperty("--bab-data-height", adminBarHeight, "");

	/**
	 * Add class on backend
	 */
	if (body.classList.contains('wp-admin')) {
		html.classList.add('bottom-admin-toolbar');
	}

	/**
	 * Listen keyboard keydown press
	 */
	document.addEventListener('keydown', function (event) {
		if (event.shiftKey && event.key === 'ArrowDown') {
			event.preventDefault();
			adminBar.classList.toggle('is-hidden');
		}
	});

	/**
	 * Fix tinyMCE bug - reset bar position when TinyMCE initializes
	 */
	if (typeof tinyMCE !== 'undefined' && tinyMCE.on) {
		tinyMCE.on('AddEditor', function () {
			// Reset admin bar to top when TinyMCE editor is added
			adminBar.style.top = '0';
			adminBar.style.bottom = 'auto';
		});
	}
});