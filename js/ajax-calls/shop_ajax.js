document.addEventListener("DOMContentLoaded", function () {
	fetch("../../data/shop_products.json")
		.then((response) => {
			if (!response.ok) {
				throw new Error("Network response was not ok");
			}
			return response.json();
		})
		.then((data) => {
			renderProducts(data);
		})
		.catch((error) => {
			console.error(
				"There has been a problem with your fetch operation:",
				error
			);
		});
});

function renderProducts(products) {
	const container = document.querySelector(
		".untree_co-section.product-section .container .row"
	);
	container.innerHTML = "";

	products.forEach((product) => {
		const productHTML = `
            <div class="col-12 col-md-4 col-lg-3 mb-5">
                <a class="product-item" href="#">
                    <img src="${product.image_url}" class="img-fluid product-thumbnail"/>
                    <h3 class="product-title">${product.title}</h3>
                    <strong class="product-price">$${product.price}</strong>
                    <span class="icon-cross"><img src="images/cross.svg" class="img-fluid" /></span>
                </a>
            </div>
        `;
		container.innerHTML += productHTML;
	});
}
