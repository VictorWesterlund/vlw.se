// Don't open the search dialog overlay if search page is open stand-alone
{
	const searchBox = document.querySelector("body:not(.search-dialog-open) searchbox");

	// Page is stand-alone
	if (searchBox) {
		// Shift focus to the on-page search box instead of opening search dialog on click
		const shiftSearchboxFocus = () => {
			// Override normal "open search dialog" behavior
			document.querySelector("dialog.search").close();
	
			// Shift focus to the on-page search input instead
		}

		// Bind event listener to searchbox element
		document.querySelector("body:not(.search-dialog-open) searchbox").addEventListener("click", shiftSearchboxFocus, true);
	
		// Remove event listener from searchbox element on page navigation
		mainElement.addEventListener(vv.Navigation.events.LOADING, () => {
			searchBox.removeEventListener("click", shiftSearchboxFocus);
		});
	}
}

new vv.Interactions("search");