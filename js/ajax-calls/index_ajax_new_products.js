document.addEventListener("DOMContentLoaded", function () {
	fetch("../../data/index_new_items.json")
		.then((response) => response.json())
		.then((data) => {
			renderNewProducts(data);
		})
		.catch((error) => console.error("Error loading new items:", error));
});

function renderNewProducts(products) {
	const rowContainer = document.querySelector(
		".product-section .container .row"
	);

	const columnsToKeep = 1;
	while (rowContainer.children.length > columnsToKeep) {
		rowContainer.removeChild(rowContainer.lastChild);
	}

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
