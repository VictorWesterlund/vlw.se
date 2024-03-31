class ContactForm {
	static STORAGE_KEY = "contact_form_message";

	constructor(form) {
		this.form = form;

		this.getSavedMessageAndPopulateFields();

		// Save message each time a button is pressed on a form element
		[...document.querySelectorAll("form :is(input, textarea)")].forEach(element => {
			element.addEventListener("keyup", () => this.saveMessage());
		});

		
	}

	// Get saved message as JSON from SessionStorage
	static getSavedMessage() {
		const data = window.sessionStorage.getItem(ContactForm.STORAGE_KEY);

		// Return message data as JSON
		return data ? JSON.parse(data) : {};
	}

	// Remove saved message from SessionStorage if it exists
	static removeSavedMessage() {
		return window.sessionStorage.removeItem(ContactForm.STORAGE_KEY);
	}

	// Populate from input fields with data from SessionStorage
	getSavedMessageAndPopulateFields() {
		const message = ContactForm.getSavedMessage();
		
		// Remove message and bail out if there is no saved message or if it is already sent
		if (!message) {
			return ContactForm.removeSavedMessage();
		}
		
		for (const [name, value] of Object.entries(message)) {
			this.form.querySelector(`[name="${name}"]`).value = value;
		}
	}

	// Save current message in SessionStorage
	saveMessage() {
		const message = {};

		// Copy field name and value from FormData into object
		(new FormData(this.form)).forEach((v, k) => message[k] = v);

		// Save message data to SessionStorage as JSON
		window.sessionStorage.setItem(ContactForm.STORAGE_KEY, JSON.stringify(message));
	}
}

// Initialize contact form handler
{
	const form = document.querySelector("section.form form");

	// Create a new form handler or remove any saved message if the form element can't be found
	form ? (new ContactForm(form)) : ContactForm.removeSavedMessage();
}

// Social links hover
{
	const socialElementHover = (target) => {
		const element = target.querySelector("p");

		target.classList.add("hovering");
		target.addEventListener("mousemove", (event) => {
			const x = event.layerX - (element.clientWidth / 2);
			const y = event.layerY + element.clientHeight;

			element.style.setProperty("transform", `translate(${x}px, ${y}px)`);
		});
	};

	const elements = [...document.querySelectorAll("social")];

	elements.forEach(element => {
		element.addEventListener("mouseenter", () => socialElementHover(element));

		element.addEventListener("mouseleave", () => {
			elements.forEach(element => element.classList.remove("hovering"));
		});
	});
}