new vv.Interactions("battlestation", {
	toggleGroup: (event) => {
		// Collapse self if already active and current target
		if (event.target.classList.contains("active")) {
			return event.target.classList.remove("active");
		}

		// Collapse all and open current target
		[...event.target.closest(".specs").querySelectorAll(".group")].forEach(element => element.classList.remove("active"));
		event.target.classList.add("active");
	},
	setSpecActive: (event) => {
		event.target.classList.add("active");

		event.target.addEventListener("mouseleave", () => event.target.classList.remove("active"));
	}
});

// Bind hover listeners for components in the SVGs
[...document.querySelectorAll("section.config g:not(.group)")].forEach(element => {
	element.addEventListener("mouseenter", () => {
		// Find an element in the most adjacent speclist and highlighit it
		const target = element.closest("section.config").querySelector(`.spec[data-target="${element.dataset.target}"]`);
		// Get closest specs wrapper element
		const specsElement = target.closest(".specs");
		// Spec item is part of a collection, we need to expand the group if that is the case
		const collectionElement = target.closest(".collection") ?? null;
		// Don't close the group after hove ends
		let closeGroupOnLeave = false;

		// Set fixed height on .specs wrapper to prevent glitchy page jumping when scrolled
		specsElement.style.setProperty("height", `${specsElement.offsetHeight}px`);
		target.classList.add("active");
		specsElement.classList.add("active");

		if (collectionElement) {
			// Close the group on leave if the group wasn't active before hovering
			closeGroupOnLeave = !collectionElement.previousElementSibling.classList.contains("active");

			collectionElement.previousElementSibling.classList.add("active");
		}

		// Bind hover leave listener
		element.addEventListener("mouseleave", () => {
			// Reset to initial states
			target.classList.remove("active");
			specsElement.classList.remove("active");
			specsElement.style.removeProperty("height");

			// Group was closed prior to hover, let's close it on hover leave
			if (closeGroupOnLeave) {
				collectionElement.previousElementSibling.classList.remove("active");
			}
		}, { once: true });
	});
});

// Bind event listeners for components in the spec lists
[...document.querySelectorAll("section.config .spec:not(.group)")].forEach(element => {
	element.addEventListener("mouseenter", () => {
		const svgTarget = element.closest("section.config").querySelector(`svg`);
		const target = svgTarget.querySelector(`svg g[data-target="${element.dataset.target}"]`);

		svgTarget.classList.add("active");
		target.classList.add("active");

		element.addEventListener("mouseleave", () => {
			svgTarget.classList.remove("active");
			target.classList.remove("active");
		}, { once: true });
	});
});