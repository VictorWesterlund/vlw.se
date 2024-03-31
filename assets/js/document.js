new vv.Interactions("document");

const mainElement = document.querySelector(vv._env.MAIN);

// Crossfade pages on navigation
// Or maybe I shouldn't... hmmm
/*mainElement.addEventListener(vv.Navigation.events.LOADING, () => {
	mainElement.classList.add("loading");

	// Clean up modified transform-origin if set after search dialog animation
	mainElement.style.removeProperty("transform-origin");
});

mainElement.addEventListener(vv.Navigation.events.LOADED, () => {
	[...document.querySelectorAll("dialog")].forEach(element => element.close())

	// Wait 200ms for the page fade-in animation to finish
	setTimeout(() => mainElement.classList.remove("loading"), 200);
});*/

// Search dialog open/close logic
{
	const CLASNAME_DIALOG_OPEN = "search-dialog-open";
	// Offset in pixels from scroll position when scaling the main element
	const TRANSFORM_ORIGIN_Y_PADDING = 350;

	const dialog = document.querySelector("dialog.search");

	// "Polyfill" for HTMLDialogELement open and close events
	(new MutationObserver((mutations) => {
		// There is only one search dialog elemenet
		const target = mutations[0].target;

		// Set or unset dialog open class on body depending on dialog visibility
		target.hasAttribute("open")
			? target.dispatchEvent(new Event("open"))
			: target.dispatchEvent(new Event("close"));

	}).observe(dialog, { attributes: true }));

	dialog.addEventListener("open", () => {
		// Scale main element from the current scroll position
		mainElement.style.setProperty("transform-origin", `50% calc(${window.scrollY}px + ${TRANSFORM_ORIGIN_Y_PADDING}px)`);

		document.body.classList.add(CLASNAME_DIALOG_OPEN);
	});
	dialog.addEventListener("close", () => document.body.classList.remove(CLASNAME_DIALOG_OPEN));
	
	// Close search dialog if dialog is clicked outside inner content
	dialog.addEventListener("click", (event) => event.target === dialog ? dialog.close() : null);

	// Open search dialog when searchbox is clicked
	document.querySelector("searchbox").addEventListener("click", () => dialog.showModal());
}

// Search logic
{
	const searchResultsElement = document.querySelector("search-results");
	const search = (query) => {
		new vv.Navigation(`/search?q=${query}`, {
			carrySearchParams: true
		}).navigate(searchResultsElement);
	};

	// Run search on keyup
	document.querySelector("search input").addEventListener("keyup", (event) => search(event.target.value));

	// Trigger expand search box animation 
	document.querySelector("search input").addEventListener("keydown", () => {
		searchResultsElement.closest("dialog").classList.add("active");
	}, { once: true });
}