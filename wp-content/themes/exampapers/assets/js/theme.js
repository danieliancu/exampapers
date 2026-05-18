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
