$(document).on("popularProductsPageLoaded", function () {
	fetchPopularProducts();
});

function fetchPopularProducts() {
	fetch(
		"http://localhost/web-intro/backend/scripts/products/popular_products.php"
	)
		.then((response) => {
			if (!response.ok) {
				throw new Error("Network response was not ok");
			}
			return response.json();
		})
		.then((data) => {
			renderPopularProducts(data);
		})
		.catch((error) => {
			console.error(
				"There has been a problem with your fetch operation:",
				error
			);
		});
}

function renderPopularProducts(products) {
	const container = document.querySelector(".popular-product .container .row");
	if (!container) {
		console.error("Container for popular products not found.");
		return;
	}
	container.innerHTML = products
		.map(
			(product) => `
        <div class="col-12 col-md-6 col-lg-4 mb-4 mb-lg-0">
            <div class="product-item-sm d-flex">
                <div class="thumbnail">
                    <img src="${product.image_url}" alt="Image" class="img-fluid" />
                </div>
                <div class="pt-3">
                    <h3>${product.title}</h3>
                    <p><a >$${product.price}</a></p>
                </div>
            </div>
        </div>
    `
		)
		.join("");
}

$(window).on("hashchange", function () {
	if (window.location.hash === "#home") {
		$(document).trigger("popularProductsPageLoaded");
	}
});

$(document).ready(function () {
	if (window.location.hash === "#home") {
		$(document).trigger("popularProductsPageLoaded");
	}
});
