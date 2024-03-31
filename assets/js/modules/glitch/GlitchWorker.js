importScripts("./Generator.mjs");

class GlitchWorker extends Generator {
	constructor() {
		super();

		// Delay between these values
		this.config = {
			glitch: { min: 500, max: 2500 },
			randBg: { min: 5000, max: 5000 }
		}

		this._timers = {};

		self.addEventListener("message", event => this.message(event));
		self.postMessage("READY");
	}

	// Run a scoped function on a random interval between
	queue(func) {
		clearTimeout(this._timers[func]);
		const next = Generator.randInt(this.config[func].min, this.config[func].max);
		this._timers[func] = setTimeout(() => this.queue(func), next);

		this[func]?.();
	}

	// Set background by id and stop randBg animation
	async forceBg(id) {
		clearTimeout(this._timers.randBg);

		const image = await this.fetchBg(id);
		this.bg.current = image;

		this.setBg(image);
	}

	// Event handler for messages from parent thread
	message(event) {
		const data = typeof event.data === "object" ? event.data : [event.data];

		switch(data[0]) {
			case "START":
				this.bg.dir = data[1];
				this.randBg();
				for(const func of Object.keys(this.config)) {
					this.queue(func);
				}
				break;
		}
	}
}

self.glitch = new GlitchWorker();