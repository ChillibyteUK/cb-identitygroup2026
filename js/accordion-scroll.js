/**
 * Simple accordion with scroll-to-top
 */
document.addEventListener('DOMContentLoaded', function() {
	// Match hub-accordion behavior: scroll before open with the same offset
	const NAV_HEIGHT = 105;
	const BUFFER = 0;

	function scrollToHeader(header) {
		if (!header) return;
		const y = header.getBoundingClientRect().top + window.pageYOffset - NAV_HEIGHT + BUFFER;
		window.scrollTo({ top: y, behavior: 'smooth' });
	}

	// Custom accordion (non-Bootstrap): scroll on open
	document.addEventListener('click', function(e) {
		const button = e.target.closest('[data-toggle-content]');
		if (!button) return;

		const target = document.querySelector(button.getAttribute('data-toggle-content'));
		if (!target) return;

		const isOpening = !target.classList.contains('is-open');

		// Toggle the content (no closing of other items)
		target.classList.toggle('is-open');

		// Scroll to the button immediately if opening
		if (isOpening) {
			const header = button.closest('.accordion-header');
			scrollToHeader(header);
		}
	});

	// Bootstrap accordion: scroll on click before the collapse runs (so header stays visible)
	document.addEventListener('click', function(e) {
		const button = e.target.closest('.accordion-button[data-bs-toggle="collapse"]');
		if (!button) return;
		const header = button.closest('.accordion-header') || button;
		scrollToHeader(header);
	});

	// Fallback: also scroll when collapse starts (in case of programmatic open)
	document.addEventListener('show.bs.collapse', function(e) {
		const collapseEl = e.target;
		if (!collapseEl) return;
		const item = collapseEl.closest('.accordion-item');
		if (!item) return;
		const button = item.querySelector('.accordion-header .accordion-button') || item.querySelector('.accordion-button');
		if (!button) return;
		const header = button.closest('.accordion-header') || button;
		scrollToHeader(header);
	});
});


