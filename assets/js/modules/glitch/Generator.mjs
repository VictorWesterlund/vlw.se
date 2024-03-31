// Fetch and create glitchy background effects
class Generator {
	constructor() {
		this.bg = {
			_this: this,
			_image: null,
			_dir: location,
			_dir_rel: "assets/media/glitch_b64/",
			count: 4,
			// Get or set current background
			get current () { return this._image; },
			set current (image) {
				this._image = image;
				this._this.setBg(image);
			},
			// Get or set the path to where base64 images are stored
			get dir () { return this._dir; },
			set dir (newPath) {
				const url = new URL(newPath);

				// Replace pathname of this file with relative path to assets
				const path = url.pathname.split("/");
				path[path.length - 1] = this._dir_rel;

				url.pathname = path.join("/");
				this._dir = url.toString();
			}
		}
	}

	// Genrate random int in range
	static randInt(min, max) {
		if(min === max) return min;
		return Math.round(Math.random() * (max - min) + min);
	}

	// Generate random string of length from charset
	static randStr(length = 2) {
		const charset = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		let output = "";
		for(let i = 0; i < length; i++) {
			output += charset.charAt(Math.floor(Math.random() * charset.length));
		}
		return output;
	}

	// Give generated background image to parent thread
	setBg(image) {
		if(typeof image !== "string") throw new TypeError("Image must be of type 'string'");
		postMessage(["BG_UPDATE", image]);
	}

	// Generate and set a glitchy image
	glitch() {
		if(!this.bg.current) return;
		const image = this.bg.current.replaceAll(Generator.randStr(), Generator.randStr());
		this.setBg(image);
	}

	// Fetch a base64 encoded background image
	async fetchBg(id) {
		const url = new URL(this.bg.dir);

		url.pathname += id + ".txt";

		const image = await fetch(url);
		if(!image.ok) throw new Error("Failed to fetch background image");

		return image.text();
	}

	// Load a random background from the image set
	async randBg() {
		const id = Generator.randInt(1, this.bg.count);

		const image = await this.fetchBg(id);
		this.bg.current = image;
	}
}