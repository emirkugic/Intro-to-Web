document.addEventListener("DOMContentLoaded", function () {
	var selectElement = document.getElementById("c_country");

	fetch("https://restcountries.com/v3.1/all")
		.then(function (response) {
			return response.json();
		})
		.then(function (data) {
			var sortedCountries = data.sort(function (a, b) {
				return a.name.common.localeCompare(b.name.common);
			});

			sortedCountries.forEach(function (country) {
				var option = document.createElement("option");
				option.value = country.name.common;
				option.textContent = country.name.common;
				selectElement.appendChild(option);
			});
		})
		.catch(function (error) {
			console.error("Error fetching the countries:", error);
		});
});
