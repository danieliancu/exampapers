(function () {
	'use strict';

	var toggle = document.querySelector('.exampapers-filter-toggle');
	var filters = document.getElementById('exampapers-shop-filters');

	if (!toggle || !filters) {
		return;
	}

	toggle.addEventListener('click', function () {
		var isOpen = filters.classList.toggle('is-open');
		toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
	});
}());
