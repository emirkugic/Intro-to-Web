$(document).on("checkoutPageLoaded", function () {
	fetchCheckoutCartContents();
});

function fetchCheckoutCartContents() {
	fetch("../../data/cart.json")
		.then((response) => {
			if (!response.ok) {
				throw new Error("Network response was not ok");
			}
			return response.json();
		})
		.then((data) => {
			updateOrderTable(data.items);
		})
		.catch((error) => {
			console.error("Error:", error);
		});
}

function updateOrderTable(items) {
	const orderTableBody = document.querySelector(
		".site-block-order-table tbody"
	);
	if (!orderTableBody) {
		console.error("Order table body not found.");
		return;
	}
	let cartSubtotal = 0;

	orderTableBody.innerHTML = "";

	items.forEach((item) => {
		cartSubtotal += item.price * item.quantity;
		const row = `
      <tr>
        <td>${item.name} <strong class="mx-2">x</strong> ${item.quantity}</td>
        <td>$${(item.price * item.quantity).toFixed(2)}</td>
      </tr>
    `;
		orderTableBody.insertAdjacentHTML("beforeend", row);
	});

	const totalRow = `
    <tr>
      <td class="text-black font-weight-bold">
        <strong>Order Total</strong>
      </td>
      <td class="text-black font-weight-bold">
        <strong>$${cartSubtotal.toFixed(2)}</strong>
      </td>
    </tr>
  `;
	orderTableBody.insertAdjacentHTML("beforeend", totalRow);
}
