new vv.Interactions("about");

const randomIntFromInterval = (min, max) => {
	return Math.floor(Math.random() * (max - min + 1) + min);
}

// Interest explosion effect from origin position
const explodeInterests = (originX, originY) => {
	const wrapper = document.querySelector("div.interests");
	wrapper.classList.add("active");

	// Elements can not translate more than negative- and positive from this number
	const transLimit = 350;

	[...wrapper.querySelectorAll("p")].forEach(element => {
		/*
			Generate random visuals for current element
		*/
		const hue = randomIntFromInterval(0, 360);
		const rotate = randomIntFromInterval(-5, 5);
		const transX = randomIntFromInterval(transLimit * -1, transLimit);
		const transY = randomIntFromInterval(transLimit * -1, transLimit);

		// Set initial position
		element.style.setProperty("top", `${originY}px`);
		element.style.setProperty("left", `${originX}px`);

		// Set random HUE rotation
		element.style.setProperty("-webkit-filter", `hue-rotate(${hue}deg)`);

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

// Bind trigger element for interests explosion and implotion
const interestsElement = document.querySelector("section.about span.interests");
interestsElement.addEventListener("mouseenter", () => {
	/*
		Magic numbers to offset the explosion initial position to accommodate larger elements
	*/
	const x = interestsElement.offsetLeft - 80;
	const y = interestsElement.offsetTop - 10;

	explodeInterests(x, y);
});

interestsElement.addEventListener("mouseleave", () => implodeInterests());