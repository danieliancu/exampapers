(function () {
	'use strict';

	var navToggle = document.querySelector('.exampapers-nav-toggle');
	var siteHeader = document.querySelector('.exampapers-header');

	if (navToggle && siteHeader) {
		navToggle.addEventListener('click', function () {
			var isOpen = siteHeader.classList.toggle('exampapers-header--open');
			navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
			navToggle.setAttribute('aria-label', isOpen ? 'Close menu' : 'Open menu');
		});

		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && siteHeader.classList.contains('exampapers-header--open')) {
				siteHeader.classList.remove('exampapers-header--open');
				navToggle.setAttribute('aria-expanded', 'false');
				navToggle.setAttribute('aria-label', 'Open menu');
				navToggle.focus();
			}
		});

		document.addEventListener('click', function (e) {
			if (!siteHeader.contains(e.target) && siteHeader.classList.contains('exampapers-header--open')) {
				siteHeader.classList.remove('exampapers-header--open');
				navToggle.setAttribute('aria-expanded', 'false');
				navToggle.setAttribute('aria-label', 'Open menu');
			}
		});
	}

	document.querySelectorAll('.exampapers-faq-list details, .exampapers-product-sections details').forEach(function (details) {
		details.addEventListener('toggle', function () {
			if (!details.open || !details.parentElement) {
				return;
			}

			details.parentElement.querySelectorAll('details[open]').forEach(function (sibling) {
				if (sibling !== details) {
					sibling.removeAttribute('open');
				}
			});
		});
	});

	document.querySelectorAll('[data-exampapers-autocomplete]').forEach(function (autocomplete) {
		var input = autocomplete.querySelector('input[type="search"]');
		var searchValue = autocomplete.querySelector('[data-exampapers-search-value]');
		var suggestions = autocomplete.querySelector('.exampapers-search-suggestions');

		if (!input) {
			return;
		}

		var buttons = suggestions ? Array.prototype.slice.call(suggestions.querySelectorAll('[data-suggestion]')) : [];
		var suppressNextFocusSuggestions = false;

		function closeSuggestions() {
			if (!suggestions) {
				return;
			}

			suggestions.hidden = true;
			input.setAttribute('aria-expanded', 'false');
		}

		function openSuggestions() {
			if (!suggestions) {
				return;
			}

			suggestions.hidden = false;
			input.setAttribute('aria-expanded', 'true');
		}

		function updateSuggestions() {
			var query = input.value.trim().toLowerCase();
			var visibleCount = 0;

			if (searchValue) {
				searchValue.value = input.value;
			}

			if (!suggestions) {
				return;
			}

			buttons.forEach(function (button) {
				var value = button.getAttribute('data-suggestion') || '';
				var isVisible = !query || value.toLowerCase().indexOf(query) !== -1;

				button.parentElement.hidden = !isVisible;

				if (isVisible) {
					visibleCount += 1;
				}
			});

			if (visibleCount) {
				openSuggestions();
			} else {
				closeSuggestions();
			}
		}

		input.addEventListener('input', updateSuggestions);
		input.addEventListener('focus', function () {
			if (suppressNextFocusSuggestions) {
				suppressNextFocusSuggestions = false;
				return;
			}

			updateSuggestions();
		});
		input.addEventListener('keydown', function (event) {
			if (event.key === 'Escape') {
				closeSuggestions();
			}
		});

		buttons.forEach(function (button) {
			button.addEventListener('click', function () {
				input.value = button.getAttribute('data-suggestion') || button.textContent.trim();
				if (searchValue) {
					searchValue.value = input.value;
				}
				closeSuggestions();
				suppressNextFocusSuggestions = true;
				input.focus();
			});
		});

		if (autocomplete.form) {
			autocomplete.form.addEventListener('submit', function () {
				if (searchValue) {
					searchValue.value = input.value;
				}
			});
		}

		document.addEventListener('click', function (event) {
			if (!autocomplete.contains(event.target)) {
				closeSuggestions();
			}
		});
	});

	document.querySelectorAll('[data-exampapers-dependent-filters]').forEach(function (form) {
		var data = form.querySelector('[data-exampapers-filter-matches]');
		var schoolData = form.querySelector('[data-exampapers-area-schools]');
		var areaSchoolsOutput = form.querySelector('[data-exampapers-area-schools-output]');
		var filterWrapper = form.querySelector('.exampapers-hero-filters');
		var selects = Array.prototype.slice.call(form.querySelectorAll('select[name="exam_level"], select[name="exam_area"], select[name="subject"]'));
		var matches = [];
		var areaSchools = {};
		var areaRequiredLevel = filterWrapper ? filterWrapper.getAttribute('data-exampapers-area-required-level') || '' : '';
		var levelSelect = form.querySelector('select[name="exam_level"]');
		var areaSelect = form.querySelector('select[name="exam_area"]');
		var subjectSelect = form.querySelector('select[name="subject"]');

		if (!data || !selects.length) {
			return;
		}

		try {
			matches = JSON.parse(data.textContent || '[]');
		} catch (error) {
			matches = [];
		}

		if (schoolData) {
			try {
				areaSchools = JSON.parse(schoolData.textContent || '{}');
			} catch (error) {
				areaSchools = {};
			}
		}

		if (!Array.isArray(matches) || !matches.length) {
			return;
		}

		function getSelection(exceptName) {
			var selection = {};

			selects.forEach(function (select) {
				if (select.name !== exceptName && select.value) {
					selection[select.name] = select.value;
				}
			});

			return selection;
		}

		function matchIncludes(match, key, value) {
			return Array.isArray(match[key]) && match[key].indexOf(value) !== -1;
		}

		function matchesSelection(match, selection) {
			return Object.keys(selection).every(function (key) {
				return matchIncludes(match, key, selection[key]);
			});
		}

		function isValidOption(select, value) {
			var selection = getSelection(select.name);

			if (select.name === 'exam_area') {
				if (areaRequiredLevel && selection.exam_level && selection.exam_level !== areaRequiredLevel) {
					return false;
				}

				if (areaRequiredLevel && !selection.exam_level) {
					return false;
				}

				if (areaRequiredLevel && selection.exam_level === areaRequiredLevel && !selection.subject) {
					return true;
				}
			}

			return matches.some(function (match) {
				return matchesSelection(match, selection) && matchIncludes(match, select.name, value);
			});
		}

		function updateAreaSchools() {
			var schools;

			if (!areaSchoolsOutput || !areaSelect) {
				return;
			}

			schools = areaSelect.value && Array.isArray(areaSchools[areaSelect.value]) ? areaSchools[areaSelect.value] : [];

			areaSchoolsOutput.innerHTML = '';

			if (!schools.length) {
				areaSchoolsOutput.hidden = true;
				return;
			}

			var list = document.createElement('ul');

			schools.forEach(function (school) {
				if (!school || !school.name) {
					return;
				}

				var item = document.createElement('li');
				item.textContent = school.name;
				list.appendChild(item);
			});

			areaSchoolsOutput.appendChild(list);
			areaSchoolsOutput.hidden = false;
		}

		function updateFilterAvailability() {
			var areaDisabled = !levelSelect || !levelSelect.value;
			var subjectDisabled = !areaSelect || !areaSelect.value || areaDisabled;

			if (areaSelect) {
				if (areaDisabled && areaSelect.value) {
					areaSelect.value = '';
				}

				areaSelect.disabled = areaDisabled;
				areaSelect.setAttribute('aria-disabled', areaDisabled ? 'true' : 'false');
			}

			if (subjectSelect) {
				if (subjectDisabled && subjectSelect.value) {
					subjectSelect.value = '';
				}

				subjectSelect.disabled = subjectDisabled;
				subjectSelect.setAttribute('aria-disabled', subjectDisabled ? 'true' : 'false');
			}
		}

		function updateSelectOptions() {
			updateFilterAvailability();

			selects.forEach(function (select) {
				Array.prototype.slice.call(select.options).forEach(function (option) {
					var isSelected = option.selected;
					var isValid = !option.value || isValidOption(select, option.value) || isSelected;

					option.hidden = !isValid;
					option.disabled = !isValid;
				});
			});

			updateAreaSchools();
		}

		selects.forEach(function (select) {
			select.addEventListener('change', function () {
				updateSelectOptions();
			});
		});

		updateSelectOptions();
	});

	function getProductId(button) {
		if (!button) {
			return '';
		}

		return button.getAttribute('data-product_id') || button.getAttribute('data-product-id') || button.value || '';
	}

	function markButtonInCart(button) {
		if (!button) {
			return;
		}

		button.classList.add('exampapers-in-cart');
		button.classList.add('disabled');
		button.textContent = 'Added to cart';
		button.setAttribute('aria-label', 'Added to cart');
		button.setAttribute('aria-disabled', 'true');
		button.setAttribute('tabindex', '-1');

		if ('disabled' in button) {
			button.disabled = true;
		}
	}

	function markProductButtonsInCart(productId) {
		if (!productId) {
			return;
		}

		document.querySelectorAll('[data-product_id="' + productId + '"], [data-product-id="' + productId + '"], button[name="add-to-cart"][value="' + productId + '"]').forEach(function (button) {
			markButtonInCart(button);
			button.classList.remove('exampapers-add-to-cart-clicked');
		});
	}

	document.querySelectorAll('[data-in-cart="true"], .single_add_to_cart_button').forEach(function (button) {
		if (!button.matches('[data-in-cart="true"]') && button.textContent.trim() !== 'Added to cart') {
			return;
		}

		markButtonInCart(button);
		markProductButtonsInCart(getProductId(button));
	});

	document.body.addEventListener('click', function (event) {
		var button = event.target.closest('.single_add_to_cart_button, .add_to_cart_button, .ajax_add_to_cart, .wc-block-components-product-button__button, [data-in-cart="true"]');

		if (!button) {
			return;
		}

		if (button.classList.contains('exampapers-in-cart')) {
			event.preventDefault();
			event.stopPropagation();
			return;
		}

		button.classList.add('exampapers-add-to-cart-clicked');
	});

	if (window.jQuery) {
		window.jQuery(document.body).on('added_to_cart', function (event, fragments, cartHash, button) {
			if (button && button.length) {
				markProductButtonsInCart(getProductId(button.get(0)));
			}

			document.querySelectorAll('.exampapers-add-to-cart-clicked').forEach(function (clickedButton) {
				markProductButtonsInCart(getProductId(clickedButton));
			});
		});
	}

	document.addEventListener('wc-blocks_added_to_cart', function () {
		document.querySelectorAll('.exampapers-add-to-cart-clicked').forEach(function (clickedButton) {
			markProductButtonsInCart(getProductId(clickedButton));
		});
	});
}());
