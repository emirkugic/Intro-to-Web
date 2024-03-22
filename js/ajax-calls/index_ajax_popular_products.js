document.addEventListener("DOMContentLoaded", function () {
	fetch("../../data/index_popular_products.json")
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
});

function renderPopularProducts(products) {
	const container = document.querySelector(".popular-product .container .row");
	container.innerHTML = "";

	products.forEach((product) => {
		const productHTML = `
            <div class="col-12 col-md-6 col-lg-4 mb-4 mb-lg-0">
                <div class="product-item-sm d-flex">
                    <div class="thumbnail">
                        <img src="${product.image_url}" alt="Image" class="img-fluid" />
                    </div>
                    <div class="pt-3">
                        <h3>${product.title}</h3>
                        <p><a href="#">$${product.price}</a></p>
                    </div>
                </div>
            </div>
        `;
		container.innerHTML += productHTML;
	});
}
