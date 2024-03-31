new vv.Interactions("about");

const randomIntFromInterval = (min, max) => {
	return Math.floor(Math.random() * (max - min + 1) + min);
}

// Interest explosion effect from origin position
const explodeInterests = (originX, originY) => {
	// Elements can not translate more than negative- and positive from this number
	const TRANS_LIMIT = 300;

	const wrapper = document.querySelector("div.interests");
	wrapper.classList.add("active");

	[...wrapper.querySelectorAll("p")].forEach(element => {
		/*
			Generate random visuals for current element
		*/
		const hue = randomIntFromInterval(0, 360);
		const rotate = randomIntFromInterval(-5, 5);
		const transX = randomIntFromInterval(TRANS_LIMIT * -1, TRANS_LIMIT);
		const transY = randomIntFromInterval(TRANS_LIMIT * -1, TRANS_LIMIT);

		// Set initial position
		element.style.setProperty("top", `${originY}px`);
		element.style.setProperty("left", `${originX}px`);

		// Set random HUE rotation
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

	[...wrapper.querySelectorAll("p")].forEach(element => {
		// Reset to initial position
		element.style.setProperty("transform", "translate(0, 0)");
	});
};

// Bind triggers for interests explosion and implotion
{
	const interestsElement = document.querySelector("section.about span.interests");
	// Bind mouse or touch events depending on pointer type of device
	const canHover = window.matchMedia("(pointer: fine)").matches;

	interestsElement.addEventListener(canHover ? "mouseenter" : "touchstart", () => {
		// Get absolute position of the trigger element
		const size = interestsElement.getBoundingClientRect();

		const x = size.x - 80;
		const y = size.y - 10;

		explodeInterests(x, y);
	});

	interestsElement.addEventListener(canHover ? "mouseleave" : "touchend", () => implodeInterests());
}
