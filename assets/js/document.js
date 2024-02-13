new vv.Interactions("document");

const mainElement = document.querySelector(vv._env.MAIN);

// Crossfade pages on navigation
mainElement.addEventListener(vv.Navigation.events.LOADING, () => mainElement.classList.add("loading"));
mainElement.addEventListener(vv.Navigation.events.LOADED, () => {
	// Wait 200ms for the page fade-in animation to finish
	setTimeout(() => mainElement.classList.remove("loading"), 200);
});