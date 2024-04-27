$(document).on("cartPageLoaded", function () {
	waitForElement(".site-blocks-table table tbody", function () {
		fetchCartContents();
		attachEventListeners();
	});
});

function fetchCartContents() {
	const url =
		"http://localhost/web-intro/backend/scripts/cart/get_all_from_cart_by_user_id.php";
	const data = JSON.stringify({ user_id: 1 });

	fetch(url, {
		method: "POST",
		headers: {
			"Content-Type": "application/json",
		},
		body: data,
	})
		.then((response) => response.json())
		.then((cart) => renderCart(cart.items))
		.catch((error) => console.error("Error fetching cart:", error));
}

function renderCart(items) {
	const tbody = document.querySelector(".site-blocks-table table tbody");
	tbody.innerHTML = items
		.map((item) => {
			const price = parseFloat(item.price);
			const quantity = parseInt(item.quantity);
			return `
            <tr data-cart-id="${item.id}">
                <td class="product-thumbnail"><img src="${
									item.image
								}" alt="Image" class="img-fluid" /></td>
                <td class="product-name"><h2 class="h5 text-black">${
									item.name
								}</h2></td>
                <td>$${price.toFixed(2)}</td>
                <td>
                    <div class="input-group mb-3 d-flex align-items-center quantity-container" style="max-width: 120px">
                        <button class="btn btn-outline-black decrease" type="button" data-id="${
													item.id
												}">&minus;</button>
                        <input type="text" class="form-control text-center quantity-amount" value="${quantity}" data-id="${
				item.id
			}" />
                        <button class="btn btn-outline-black increase" type="button" data-id="${
													item.id
												}">&plus;</button>
                    </div>
                </td>
                <td>$${(price * quantity).toFixed(2)}</td>
                <td><button class="btn btn-black btn-sm delete-button" data-id="${
									item.id
								}">X</button></td>
            </tr>
            `;
		})
		.join("");

	attachQuantityEventListeners();
	attachDeleteEventListeners();
	updateCartTotal(items);
}

function attachQuantityEventListeners() {
	document.querySelectorAll(".increase, .decrease").forEach((button) => {
		button.addEventListener("click", function (event) {
			event.preventDefault();
			const cartId = this.getAttribute("data-id");
			const isIncrease = this.classList.contains("increase");
			updateQuantity(cartId, isIncrease ? 1 : -1);
		});
	});
}

function updateQuantity(cartId, change) {
	const quantityInput = document.querySelector(
		`input[data-id="${cartId}"].quantity-amount`
	);
	let newQuantity = parseInt(quantityInput.value) + change;
	newQuantity = Math.max(1, newQuantity);
	quantityInput.value = newQuantity;

	fetch(
		"http://localhost/web-intro/backend/scripts/cart/update_cart_quantity_by_id.php",
		{
			method: "POST",
			headers: {
				"Content-Type": "application/json",
			},
			body: JSON.stringify({ cart_id: cartId, new_quantity: newQuantity }),
		}
	)
		.then((response) => response.json())
		.then((data) => {
			if (data.success) {
				const row = document.querySelector(`tr[data-cart-id="${cartId}"]`);
				const pricePerUnit = parseFloat(
					row.querySelector("td:nth-child(3)").textContent.replace("$", "")
				);
				row.querySelector("td:nth-child(5)").textContent = `$${(
					pricePerUnit * newQuantity
				).toFixed(2)}`;
				updateCartTotal();
			}
		})
		.catch((error) => console.error("Error updating cart quantity:", error));
}

function attachDeleteEventListeners() {
	document.querySelectorAll(".delete-button").forEach((button) => {
		button.addEventListener("click", function (event) {
			event.preventDefault();
			const cartId = this.getAttribute("data-id");
			deleteCartItem(cartId);
		});
	});
}

function deleteCartItem(cartId) {
	fetch(
		"http://localhost/web-intro/backend/scripts/cart/delete_from_cart_by_id.php",
		{
			method: "POST",
			headers: {
				"Content-Type": "application/json",
			},
			body: JSON.stringify({ cart_id: cartId }),
		}
	)
		.then((response) => response.json())
		.then((data) => {
			if (data.success) {
				const row = document.querySelector(`tr[data-cart-id="${cartId}"]`);
				if (row) {
					row.remove();
				}
				updateCartTotal();
			}
		})
		.catch((error) => console.error("Error deleting cart item:", error));
}

function waitForElement(selector, callback) {
	const element = document.querySelector(selector);
	if (element) {
		callback();
	} else {
		setTimeout(() => waitForElement(selector, callback), 500);
	}
}

function updateCartTotal() {
	const items = document.querySelectorAll(".site-blocks-table table tbody tr");
	let total = 0;
	items.forEach((item) => {
		const price = parseFloat(
			item.querySelector("td:nth-child(3)").textContent.replace("$", "")
		);
		const quantity = parseInt(item.querySelector(".quantity-amount").value);
		total += price * quantity;
	});

	const totalElement = document.querySelector(
		".col-md-6.text-right strong.text-black"
	);
	if (totalElement) {
		totalElement.textContent = `$${total.toFixed(2)}`;
	} else {
		console.error("The total price element was not found in the DOM.");
	}
}
