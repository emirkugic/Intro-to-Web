$(document).on("newProductsPageLoaded", function () {
	fetchNewProducts();
});

function fetchNewProducts() {
	fetch(
		"http://localhost/web-intro/backend/products/all"
		// "../../backend/scripts/products/get_new_products.php"
	)
		.then((response) => response.json())
		.then((data) => {
			renderNewProducts(data);
		})
		.catch((error) => console.log("Error loading new items:", error));
}

function renderNewProducts(products) {
	const rowContainer = document.querySelector(
		".product-section .container .row"
	);
	if (!rowContainer) {
		console.error("Container for new products not found.");
		return;
	}

	const columnsToKeep = 1;
	while (rowContainer.children.length > columnsToKeep) {
		rowContainer.removeChild(rowContainer.lastChild);
	}

	// slice to show only 4 products
	products = products.slice(0, 3);

	products.forEach((product) => {
		const productColumnHTML = `
            <div class="col-12 col-md-4 col-lg-3 mb-5 mb-md-0">
                <a class="product-item" href="${product.link}">
                    <img src="${product.image_url}" class="img-fluid product-thumbnail"/>
                    <h3 class="product-title">${product.title}</h3>
                    <strong class="product-price">$${product.price}</strong>
                    <span class="icon-cross"><img src="images/cross.svg" class="img-fluid" /></span>
                </a>
            </div>
        `;
		rowContainer.insertAdjacentHTML("beforeend", productColumnHTML);
	});
}

$(window).on("hashchange", function () {
	if (window.location.hash === "#home") {
		$(document).trigger("newProductsPageLoaded");
	}
});

$(document).ready(function () {
	if (window.location.hash === "" || window.location.hash === "#home") {
		$(document).trigger("newProductsPageLoaded");
	}
});
