// Listen for a custom event 'checkoutPageLoaded' to handle user data fetching for the checkout
$(document).on("checkoutPageLoaded", function () {
	fetchCheckoutUserData();
});

function fetchCheckoutUserData() {
	fetch("../../data/checkout_user_data.json")
		.then((response) => response.json())
		.then((userData) => {
			populateUserData(userData);
		})
		.catch((error) => {
			console.error("Error fetching user data:", error);
		});
}

function populateUserData(userData) {
	if (Object.keys(userData).length !== 0) {
		if (userData.country)
			document.getElementById("c_country").value = userData.country;
		if (userData.firstName)
			document.getElementById("c_fname").value = userData.firstName;
		if (userData.lastName)
			document.getElementById("c_lname").value = userData.lastName;
		if (userData.companyName)
			document.getElementById("c_companyname").value = userData.companyName;
		if (userData.address)
			document.getElementById("c_address").value = userData.address;
		if (userData.stateCountry)
			document.getElementById("c_state_country").value = userData.stateCountry;
		if (userData.postalZip)
			document.getElementById("c_postal_zip").value = userData.postalZip;
		if (userData.emailAddress)
			document.getElementById("c_email_address").value = userData.emailAddress;
		if (userData.phone)
			document.getElementById("c_phone").value = userData.phone;
		// No need to populate password
		if (userData.orderNotes)
			document.getElementById("c_order_notes").value = userData.orderNotes;
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
