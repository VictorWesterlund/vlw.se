new vv.Interactions("document", {
	navigateHome: () => new vv.Navigation("/").navigate(),
	closeSearchbox: () => document.querySelector("header").classList.remove("searchboxActive"),
	openSearchbox: () => {
		document.querySelector("header").classList.add("searchboxActive");
		// Select searchbox inner input element
		document.querySelector("searchbox input").focus();
	}
});

// Crossfade pages on navigation
{
	const mainElement = document.querySelector(vv._env.MAIN);

	mainElement.addEventListener(vv.Navigation.events.LOADING, () => {
		mainElement.classList.add("loading");
	});

	mainElement.addEventListener(vv.Navigation.events.LOADED, () => {
		// Close searchbox on main page navigation
		document.querySelector("header").classList.remove("searchboxActive");

		// Wait 200ms for the page fade-in animation to finish
		setTimeout(() => mainElement.classList.remove("loading"), 200);
	});
}

// Handle search logic
{
	const searchResultsElement = document.querySelector("search-results");

	document.querySelector("header input[type='search']").addEventListener("input", (event) => {
		// Debounce user input
		clearTimeout(event.target._throttle);
		event.target._throttle = setTimeout(() => {
			// Navigate search-results element on user input
			new vv.Navigation(`/search?q=${event.target.value}`).navigate(searchResultsElement);
		}, 100);
	});
}