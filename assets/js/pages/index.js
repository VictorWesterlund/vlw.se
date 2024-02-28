new vv.Interactions("index", {
	// Copy email address to clipboard
	copyEmail: async () => {
		try {
			await navigator.clipboard.writeText("victor@vlw.se");

			[...document.querySelectorAll("[vv-call='copyEmail']")].forEach(element => {
				element.classList.add("copied");

				setTimeout(() => element.classList.remove("copied"), 1200);
			});
		} catch (error) {
			console.error(error.message);
		}
	},
	// Open the fullscreen menu
	openMenu: () => document.querySelector("menu").classList.add("active"),
	// Close the fullscreen menu
	closeMenu: () => document.querySelector("menu").classList.remove("active")
});

// Change site accent color on hover of menu items
if (window.matchMedia("(hover: hover)")) {
	// Update root CSS variables
	const updateColor = (rgb = null, hue = 0) => {
		if (!rgb) {
			document.documentElement.style.removeProperty("--hue-accent");
			document.documentElement.style.removeProperty("--primer-color-accent");
			document.documentElement.style.removeProperty("--color-accent");

			return;
		}

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

// Open search box from mobile fullscreen menu
{
	// Open search dialog when searchbox is clicked
	document.querySelector("menu searchbox").addEventListener("click", () => {
		// Search box dialog element
		document.querySelector("dialog.search").showModal();

		// Close fullscreen menu
		document.querySelector("menu").classList.remove("active");
	});
}