new vv.Interactions("about");

const randomIntFromInterval = (min, max) => {
	return Math.floor(Math.random() * (max - min + 1) + min);
}

// Interest explosion effect from origin position
const explodeInterests = (originX, originY) => {
	const wrapper = document.querySelector("div.interests");
	wrapper.classList.add("active");

	// Elements can not expand further than positive or negative of these values
	const transLimitX = window.innerWidth / 4;
	const transLimitY = window.innerHeight / 3;

	[...wrapper.querySelectorAll("p")].forEach(element => {
		const size = element.getBoundingClientRect();
		
		// Generate random HUE wheel rotation degrees
		const hue = randomIntFromInterval(0, 360);
		// Generate random element transform rotation
		const rotate = randomIntFromInterval(-5, 5);

		// Generate random offsets in each direction clamped to translation limit
		let transX = randomIntFromInterval(transLimitX * -1, transLimitX);
		let transY = randomIntFromInterval(transLimitY * -1, transLimitY);
	
		// Clamp translation to screen left and right X size
		transX = Math.max(0 - originX, Math.min((window.innerWidth - originX) - size.width, transX));
		// Clamp translation to top and bottom Y size
		transY = Math.max(0 - originY, Math.min((window.innerHeight - originY) - size.height, transY));

		// Set initial position
		element.style.setProperty("top", `${originY}px`);
		element.style.setProperty("left", `${originX}px`);

		// Set HUE rotation
		element.style.setProperty("-webkit-filter", `hue-rotate(${hue}deg)`);
		element.style.setProperty("filter", `hue-rotate(${hue}deg)`);

		// Translate and rotate to random position from origin
		element.style.setProperty("transform", `translate(${transX}px, ${transY}px) rotate(${rotate}deg)`);
	});
};

// Interest implotion effect from explodeInterests()
const implodeInterests = () => {
	const wrapper = document.querySelector("div.interests");
	wrapper.classList.remove("active");

	// Reset to initial position
	[...wrapper.querySelectorAll("p")].forEach(element => element.style.setProperty("transform", "translate(0, 0)"));
};

// Bind triggers for interests explosion and implotion
{
	const interestsElement = document.querySelector("section.about span.interests");
	// Bind mouse or touch events depending on pointer type of device
	const canHover = window.matchMedia("(pointer: fine)").matches;

	interestsElement.addEventListener(canHover ? "mouseenter" : "touchstart", () => {
		// Get absolute position of the trigger element
		const size = interestsElement.getBoundingClientRect();

		explodeInterests(size.x, size.y);
	});

	interestsElement.addEventListener(canHover ? "mouseleave" : "touchend", () => implodeInterests());
}
