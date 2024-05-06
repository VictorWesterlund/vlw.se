const EMAIL_CPY_ANIM_DUR_MSECONDS = 1000;

// Run email copied splash animation
const emailCopiedAnimation = () => {
	const CONFETTI_COUNT = 40;
	const CONFETTI_SCALE_PIXELS = 300;

	const randomIntFromInterval = (min, max) => {
		return Math.floor(Math.random() * (max - min + 1) + min)
	}

	// Create new splash element
	const splashElement = document.createElement("splash");
	splashElement.innerText = "copied!";

	// Set inline display to none to hide this element on pages where the splash element has no override styles defined.
	splashElement.style.display = "none";

	// Array of box-shadow strings as "confetti"
	const confetti = [];

	// Generate random confetti
	for (let i = 0; i < CONFETTI_COUNT; i++) {
		// Random confetti position
		const x = randomIntFromInterval(CONFETTI_SCALE_PIXELS * -1, CONFETTI_SCALE_PIXELS);
		const y = randomIntFromInterval(CONFETTI_SCALE_PIXELS * -1, CONFETTI_SCALE_PIXELS);

		// Random confetti RGB color
		const rgb = [
			randomIntFromInterval(0, 255),
			randomIntFromInterval(0, 255),
			randomIntFromInterval(0, 255)
		];

		// Interpolate random values and append to outer confetti array
		confetti.push(`${x}px ${y}px 0 rgb(${rgb.join(",")})`);
	}

	// Set CSS variable on splash element that in turn will be used by pseudo-element 
	splashElement.style.setProperty("--confetti", confetti.join(","));

	// Start animation by appending the created element to the document body
	document.body.appendChild(splashElement);

	// Run hide animation
	setTimeout(() => {
		splashElement.classList.add("hide");

		// Selfdestruct element when hide animation finishes
		setTimeout(() => splashElement.remove(), 400);
	}, EMAIL_CPY_ANIM_DUR_MSECONDS + 100);
}

new vv.Interactions("index", {
	// Copy email address to clipboard
	copyEmail: async () => {
		try {
			await navigator.clipboard.writeText("victor@vlw.se");

			// Run "email copied" animation!
			emailCopiedAnimation();

			// NOTE: I don't know, spamming the button is kinda fun
			// Prevent interactions with the copy email elements while the animation is running
			/*[...document.querySelectorAll("[vv-call='copyEmail']")].forEach(element => {
				//element.classList.add("lock");

				setTimeout(() => element.classList.remove("lock"), EMAIL_CPY_ANIM_DUR_MSECONDS);
			});*/
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

	[...document.querySelectorAll("menu li")].forEach(element => {
		// Change site accent color to RGB and HUE rotation defined in element dataset
		element.addEventListener("mouseenter", (event) => updateColor(event.target.dataset.rgb, event.target.dataset.hue));
		// Reset initial accent color and hues
		element.addEventListener("mouseleave", () => updateColor());
	});

	// Reset color on navigation
	document.querySelector(vv._env.MAIN).addEventListener(vv.Navigation.events.LOADED, () => updateColor(), { once: true });
}
