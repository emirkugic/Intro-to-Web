$(document).on("shopPageLoaded", function () {
	fetch("http://localhost/web-intro/backend/products/")
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
	if (container) {
		container.innerHTML = products
			.map(
				(product) => `
                    <div class="col-12 col-md-4 col-lg-3 mb-5">
                        <div class="product-item">
                            <img src="${product.image_url}" class="img-fluid product-thumbnail"/>
                            <h3 class="product-title">${product.title}</h3>
                            <strong class="product-price">$${product.price}</strong>
                            <span class="icon-cross" data-id="${product.id}">
                                <img src="images/cross.svg" class="img-fluid" />
                            </span>
                        </div>
                    </div>
                `
			)
			.join("");

		document.querySelectorAll(".icon-cross").forEach((icon) => {
			icon.addEventListener("click", function () {
				const productId = this.getAttribute("data-id");
				addToCart(productId);
			});
		});
	} else {
		console.error("Container for products not found.");
	}
}

function addToCart(productId) {
	console.log(`Pretend to add product ID ${productId} to cart`);
}
