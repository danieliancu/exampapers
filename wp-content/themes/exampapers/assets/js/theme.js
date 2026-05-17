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
