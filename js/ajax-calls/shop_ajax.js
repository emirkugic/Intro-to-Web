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

// TODO Check if the quantity is > 0

function renderProducts(products) {
	const container = document.querySelector(
		".untree_co-section.product-section .container .row"
	);
	container.innerHTML = "";

	products.forEach((product) => {
		const productHTML = `
            <div class="col-12 col-md-4 col-lg-3 mb-5">
                <div class="product-item">
                    <img src="../${product.image_url}" class="img-fluid product-thumbnail"/>
                    <h3 class="product-title">${product.title}</h3>
                    <strong class="product-price">$${product.price}</strong>
                    <span class="icon-cross" data-id="${product.id}"><img src="../images/cross.svg" class="img-fluid" /></span>
                </div>
            </div>
        `;
		container.innerHTML += productHTML;
	});

	document.querySelectorAll(".icon-cross").forEach((icon) => {
		icon.addEventListener("click", function () {
			const productId = this.getAttribute("data-id");
			addToCart(productId);
		});
	});
}

function addToCart(productId) {
	console.log(`Pretend to add product ID ${productId} to cart`);

	// fetch("path/to/real/future/cart/api", {
	// 	method: "POST",
	// 	headers: {
	// 		"Content-Type": "application/json",
	// 	},
	// 	body: JSON.stringify({
	// 		productId: productId,
	// 		quantity: 1,
	// 	}),
	// })
	// 	.then((response) => response.json())
	// 	.then((data) => {
	// 		console.log("Success:", data);
	// 	})
	// 	.catch((error) => {
	// 		console.error("Error:", error);
	// 	});
}
