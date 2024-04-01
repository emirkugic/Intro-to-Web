function setupCountrySelect() {
	var selectElement = document.getElementById("c_country");
	if (!selectElement) {
		console.error("Country select element not found.");
		setTimeout(setupCountrySelect, 500);
		return;
	}

	fetch("https://restcountries.com/v3.1/all")
		.then(function (response) {
			return response.json();
		})
		.then(function (data) {
			populateCountrySelect(selectElement, data);
		})
		.catch(function (error) {
			console.error("Error fetching the countries:", error);
		});
}

function populateCountrySelect(selectElement, countries) {
	while (selectElement.options.length > 1) {
		selectElement.remove(1);
	}

	var sortedCountries = countries.sort(function (a, b) {
		return a.name.common.localeCompare(b.name.common);
	});

	sortedCountries.forEach(function (country) {
		var option = document.createElement("option");
		option.value = country.name.common;
		option.textContent = country.name.common;
		selectElement.appendChild(option);
	});
}

function handleCheckoutPage() {
	if (window.location.hash === "#checkout") {
		setupCountrySelect();
	}
}

$(window).on("hashchange", handleCheckoutPage);
$(document).ready(handleCheckoutPage);
