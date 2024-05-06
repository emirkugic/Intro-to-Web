function saveOrUpdateAddress() {
	const url = "http://localhost/web-intro/backend/shipping-addresses/save";
	const data = {
		country: document.getElementById("c_country").value,
		first_name: document.getElementById("c_fname").value,
		last_name: document.getElementById("c_lname").value,
		company_name: document.getElementById("c_companyname").value,
		address: document.getElementById("c_address").value,
		state: document.getElementById("c_state_country").value,
		zip_code: document.getElementById("c_postal_zip").value,
		email: document.getElementById("c_email_address").value,
		phone: document.getElementById("c_phone").value,
		save_address: document.getElementById("c_save_address").checked,
	};

	fetch(url, {
		method: "POST",
		headers: {
			"Content-Type": "application/json",
			Authorization: "Bearer " + localStorage.getItem("jwtToken"),
		},
		body: JSON.stringify(data),
	})
		.then((response) => response.json())
		.then((response) => {
			if (response.success) {
				console.log("Address saved successfully!");
			} else {
				console.error("Failed to save address.");
			}
		})
		.catch((error) => console.error("Error saving address:", error));
}
