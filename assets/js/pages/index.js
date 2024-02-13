new vv.Interactions("index");

// Change site accent color on hover of menu items
if (window.matchMedia("(hover: hover)")) {
	// Get the initial accent color RGB
	const initRgb = getComputedStyle(document.documentElement).getPropertyValue("--primer-color-accent");

	// Update root CSS variables
	const updateColor = (rgb = initRgb, hue = 0) => {
		document.documentElement.style.setProperty("--hue-accent", `${hue}deg`);

		document.documentElement.style.setProperty("--primer-color-accent", `${rgb}`);
		// Compiled color variable must to be updated to receive the new RGB values
		document.documentElement.style.setProperty("--color-accent", "rgb(var(--primer-color-accent)");
	};

	[...document.querySelectorAll(".large section.menu p")].forEach(element => {
		// Change site accent color to RGB and HUE rotation defined in element dataset
		element.addEventListener("mouseenter", (event) => updateColor(event.target.dataset.rgb, event.target.dataset.hue));
		// Reset initial accent color and hues
		element.addEventListener("mouseleave", () => updateColor());
	});

	// Reset color on navigation
	document.querySelector(vv._env.MAIN).addEventListener(vv.Navigation.events.LOADING, () => updateColor(), { once: true });
}