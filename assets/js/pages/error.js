import { default as Glitch } from "/assets/js/modules/glitch/Glitch.mjs";

// Start glitch canvas
const canvas = document.querySelector("canvas");
canvas._glitch = new Glitch(canvas);

// Text glitching
{
	const GLITCH_MAX_OFFSET_PIXELS = 5;
	const GLITCH_COUNT_MAX = 4;
	const UNSET_GLITCH_TIMEOUT = 100;

	const NEXT_GLITCH_MIN = 100;
	const NEXT_GLITCH_MAX = 500;

	const COLORS = [
		"255,0,0", // Red
		"0,0,255" // Blue
	];

	const randomIntFromInterval = (min, max) => {
		return Math.floor(Math.random() * (max - min + 1) + min)
	}
	
	const glitchText = (target) => {
		const glitch = [];
		
		// Generate text-shadow property values
		for (let i = 0; i < randomIntFromInterval(2, GLITCH_COUNT_MAX); i++) {
			// Text-shadow x offset
			const x = randomIntFromInterval(GLITCH_MAX_OFFSET_PIXELS * -1, GLITCH_MAX_OFFSET_PIXELS);

			// Get red or blue color from random parity
			const rgb = randomIntFromInterval(0, 1) ? "255,0,0" : "0,0,55";
			// Generate random decimal transparancy
			const alpha = randomIntFromInterval(30, 50) / 100;

			glitch.push(`${x}px 0 0 rgba(${rgb}, ${alpha})`);
		}

		// Glitch the text!
		target.style.setProperty("text-shadow", glitch.join(","));

		// Remove glitch effect from text
		setTimeout(() => target.style.setProperty("text-shadow", "unset"), UNSET_GLITCH_TIMEOUT);

		// Glitch the text again after this timeout
		setTimeout(() => glitchText(target), randomIntFromInterval(NEXT_GLITCH_MIN, NEXT_GLITCH_MAX));
	};
	
	[...document.querySelectorAll("[glitch-text]")].forEach(element => glitchText(element));
}