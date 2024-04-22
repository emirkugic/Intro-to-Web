$(document).on("checkoutPageLoaded", function () {
	fetchCheckoutUserData();
});

function fetchCheckoutUserData() {
	fetch(
		"http://localhost/web-intro/backend/scripts/address/get_addresses_by_user_id.php?user_id=1"
	)
		.then((response) => response.json())
		.then((data) => {
			if (data.addresses.length > 0) {
				populateUserData(data.addresses[0]); // Assuming we want to use the first address
			}
		})
		.catch((error) => {
			console.error("Error fetching user data:", error);
		});
}

function populateUserData(address) {
	if (address.country)
		document.getElementById("c_country").value = address.country;
	if (address.company_name)
		document.getElementById("c_companyname").value = address.company_name;
	if (address.address)
		document.getElementById("c_address").value = address.address;
	if (address.state)
		document.getElementById("c_state_country").value = address.state; // Assuming you have a state or country field
	if (address.zip_code)
		document.getElementById("c_postal_zip").value = address.zip_code;
	// Update other fields as needed
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
