document.addEventListener("DOMContentLoaded", function () {
	var contactForm = document.querySelector("form");
	contactForm.addEventListener("submit", function (e) {
		e.preventDefault();

		var formData = {
			firstName: document.getElementById("fname").value,
			lastName: document.getElementById("lname").value,
			email: document.getElementById("email").value,
			message: document.getElementById("message").value,
		};

		console.log("Form data to be sent:", formData);
		fetch("https://example.com/api/send", {
			method: "POST",
			headers: {
				"Content-Type": "application/json",
			},
			body: JSON.stringify(formData),
		})
			.then((response) => response.json())
			.then((data) => {
				console.log("Success:", data);
			})
			.catch((error) => {
				console.error("Error:", error);
			});

		// contactForm.reset();
	});
});
