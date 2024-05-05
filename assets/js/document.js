new vv.Interactions("document");

const mainElement = document.querySelector(vv._env.MAIN);

// Crossfade pages on navigation
// Or maybe I shouldn't... hmmm
mainElement.addEventListener(vv.Navigation.events.LOADING, () => {
	mainElement.classList.add("loading");
});

mainElement.addEventListener(vv.Navigation.events.LOADED, () => {
	[...document.querySelectorAll("dialog")].forEach(element => element.close())

	// Wait 200ms for the page fade-in animation to finish
	setTimeout(() => mainElement.classList.remove("loading"), 200);
});