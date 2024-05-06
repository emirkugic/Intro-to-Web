$(document).on("checkoutPageLoaded", function () {
	fetchCheckoutUserData();
});

function fetchCheckoutUserData() {
	const url =
		"http://localhost/web-intro/backend/shipping-addresses/full-address";
	fetch(url, {
		method: "GET",
		headers: {
			"Content-Type": "application/json",
			Authorization: "Bearer " + localStorage.getItem("jwtToken"),
		},
	})
		.then((response) => response.json())
		.then((data) => {
			if (data && data.length > 0) {
				populateUserData(data[0]);
			} else {
				console.error("No user data available");
			}
		})
		.catch((error) => {
			console.error("Error fetching user data:", error);
		});
}

function populateUserData(userData) {
	if (userData) {
		if (document.getElementById("c_country"))
			document.getElementById("c_country").value = userData.country || "";
		if (document.getElementById("c_fname"))
			document.getElementById("c_fname").value = userData.first_name || "";
		if (document.getElementById("c_lname"))
			document.getElementById("c_lname").value = userData.last_name || "";
		if (document.getElementById("c_companyname"))
			document.getElementById("c_companyname").value =
				userData.company_name || "";
		if (document.getElementById("c_address"))
			document.getElementById("c_address").value = userData.address || "";
		if (document.getElementById("c_state_country"))
			document.getElementById("c_state_country").value = userData.state || "";
		if (document.getElementById("c_postal_zip"))
			document.getElementById("c_postal_zip").value = userData.zip_code || "";
		if (document.getElementById("c_email_address"))
			document.getElementById("c_email_address").value = userData.email || "";
		if (document.getElementById("c_phone"))
			document.getElementById("c_phone").value = userData.phone || "";
		if (document.getElementById("c_order_notes"))
			document.getElementById("c_order_notes").value =
				userData.order_notes || "";
	}
}

$(window).on("hashchange", function () {
	if (window.location.hash === "#checkout") {
		$(document).trigger("checkoutPageLoaded");
	}
});

$(document).ready(function () {
	if (window.location.hash === "#checkout") {
		$(document).trigger("checkoutPageLoaded");
	}
});
