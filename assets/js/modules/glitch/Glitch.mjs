export default class Glitch {
	constructor(target) {
		this.worker = new Worker(this.getWorkerScriptURL());
		this.worker.addEventListener("message", event => this.message(event));

		this.target = target ? target : document.body;
	}

	// Update the target CSS background with an image URL
	setVisibleBg(image) {
		this.target.style.setProperty("background-image", `url(${image})`);
	}

	// Get URL for the dedicated worker
	getWorkerScriptURL() {
		const name = "GlitchWorker.js";
		const url = new URL(import.meta.url);

		// Replace pathname of this file with worker
		const path = url.pathname.split("/");
		path[path.length - 1] = name;

		url.pathname = path.join("/");
		return url.toString();
	}

	// Event handler for messages from worker thread
	message(event) {
		const data = typeof event.data === "object" ? event.data : [event.data];

		switch(data[0]) {
			case "READY":
				this.worker.postMessage(["START", new URL(location).toString()]);
				break;

			case "BG_UPDATE":
				this.setVisibleBg(data[1]);
				break;
		}
	}
}