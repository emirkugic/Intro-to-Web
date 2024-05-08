<?php

require_once __DIR__ . '/../services/CartService.class.php';
require_once __DIR__ . '/../../middleware.php';

/**
 * @OA\Schema(
 *     schema="CartItem",
 *     type="object",
 *     required={"id", "name", "price", "quantity", "image"},
 *     @OA\Property(property="id", type="integer", description="Unique identifier for the cart item"),
 *     @OA\Property(property="name", type="string", description="Name of the product in the cart"),
 *     @OA\Property(property="price", type="number", format="float", description="Price of the product"),
 *     @OA\Property(property="image", type="string", description="Image URL of the product"),
 *     @OA\Property(property="quantity", type="integer", description="Quantity of the product in the cart")
 * )
 */
Flight::group('/carts', function () {

    /**
     * @OA\Get(
     *     path="/carts/user-cart",
     *     summary="Get the cart contents for the logged-in user",
     *     tags={"Carts"},
     *     security={{"ApiKey": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Cart contents retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="items", type="array",
     *                 @OA\Items(type="object", ref="#/components/schemas/CartItem")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized access"
     *     )
     * )
     */
    Flight::route('GET /user-cart', function () {
        $user = Flight::get('user');
        if (!$user['userId']) {
            Flight::halt(401, 'Unauthorized');
        }

        $cart_service = new CartService();
        $carts = $cart_service->get_carts_with_products_by_user($user['userId']);

        $output = ['items' => []];
        foreach ($carts as $cart) {
            $output['items'][] = [
                'id' => $cart['id'],
                'name' => $cart['title'],
                'price' => $cart['price'],
                'image' => $cart['image_url'],
                'quantity' => $cart['quantity'],
            ];
        }

        Flight::json($output);
    });


    /**
     * @OA\Patch(
     *     path="/carts/update-quantity/{cart_id}",
     *     summary="Update the quantity of an item in a user's cart",
     *     tags={"Carts"},
     *     security={{"ApiKey": {}}},
     *     @OA\Parameter(
     *         name="cart_id",
     *         in="path",
     *         required=true,
     *         description="Cart item ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         description="Quantity update request",
     *         required=true,
     *         @OA\JsonContent(
     *             required={"new_quantity"},
     *             @OA\Property(property="new_quantity", type="integer", example=3)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Quantity updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Quantity updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access denied"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cart not found"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to update cart quantity"
     *     )
     * )
     */
    Flight::route('PATCH /update-quantity/@cart_id', function ($cart_id) {
        $user = Flight::get('user');
        $data = Flight::request()->data->getData();
        $new_quantity = $data['new_quantity'];

        $cart_service = new CartService();
        $cart = $cart_service->get_cart_by_id($cart_id);

        if (!$cart) {
            Flight::halt(404, 'Cart not found');
            return;
        }
        if ($user['role'] !== 'ADMIN' && $user['userId'] != $cart['user_id']) {
            Flight::halt(403, 'Access Denied');
            return;
        }

        $result = $cart_service->update_cart_quantity($cart_id, $new_quantity);
        if ($result) {
            Flight::json(['success' => true, 'message' => 'Quantity updated successfully']);
        } else {
            Flight::halt(400, 'Failed to update cart quantity');
        }
    });



    /**
     * @OA\Get(
     *     path="/carts/get-all",
     *     summary="Get all cart items (Admin only)",
     *     tags={"Carts"},
     *     security={{"ApiKey": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="All cart items retrieved successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/CartItem")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized access"
     *     )
     * )
     */
    Flight::route('GET /get-all', function () {
        authorize("ADMIN");
        $cart_service = new CartService();
        $order = Flight::request()->query['order'] ?? '-id';
        $carts = $cart_service->get_all_carts($order);
        Flight::json($carts);
    });




    /**
     * @OA\Get(
     *     path="/carts/{cart_id}",
     *     summary="Get a specific cart item by cart ID",
     *     tags={"Carts"},
     *     security={{"ApiKey": {}}},
     *     @OA\Parameter(
     *         name="cart_id",
     *         in="path",
     *         required=true,
     *         description="The ID of the cart item to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cart item retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/CartItem")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access denied"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cart item not found"
     *     )
     * )
     */
    Flight::route('GET /@cart_id', function ($cart_id) {
        $user = Flight::get('user');
        $cart_service = new CartService();
        $cart = $cart_service->get_cart_by_id($cart_id);
        if (!$cart) {
            Flight::halt(404, 'Cart not found');
            return;
        }
        if ($user['role'] !== 'ADMIN' && $user['userId'] != $cart['user_id']) {
            Flight::halt(403, 'Access Denied');
            return;
        }
        Flight::json($cart);
    });

    /**
     * @OA\Post(
     *     path="/carts/carts/add",
     *     summary="Add a new item to the cart",
     *     tags={"Carts"},
     *     security={{"ApiKey": {}}},
     *     @OA\RequestBody(
     *         description="Data required to add an item to the cart",
     *         required=true,
     *         @OA\JsonContent(
     *             required={"productId", "quantity"},
     *             @OA\Property(property="productId", type="integer", example=123),
     *             @OA\Property(property="quantity", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Item added to cart successfully",
     *         @OA\JsonContent(ref="#/components/schemas/CartItem")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to add to cart"
     *     )
     * )
     */
    Flight::route('POST /carts/add', function () {
        $data = Flight::request()->data->getData();
        $cart_service = new CartService();

        $user = Flight::get('user');

        $result = $cart_service->add_cart([
            'user_id' => $user['userId'],
            'product_id' => $data['productId'],
            'quantity' => $data['quantity']
        ]);
        if ($result) {
            Flight::json($result, 201);
        } else {
            Flight::halt(400, 'Failed to add to cart');
        }
    });



    /**
     * @OA\Put(
     *     path="/carts/{cart_id}",
     *     summary="Update a specific cart item",
     *     tags={"Carts"},
     *     security={{"ApiKey": {}}},
     *     @OA\Parameter(
     *         name="cart_id",
     *         in="path",
     *         required=true,
     *         description="The ID of the cart item to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         description="Data to update in the cart item",
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="productId", type="integer", description="Product ID associated with the cart item"),
     *             @OA\Property(property="quantity", type="integer", description="Quantity of the product"),
     *             example={"productId": 101, "quantity": 2}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cart item updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/CartItem")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access denied"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cart item not found"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to update cart item"
     *     )
     * )
     */
    Flight::route('PUT /@cart_id', function ($cart_id) {
        $user = Flight::get('user');
        $cart_service = new CartService();
        $cart = Flight::request()->data->getData();
        $existing_cart = $cart_service->get_cart_by_id($cart_id);
        if (!$existing_cart) {
            Flight::halt(404, 'Cart not found');
            return;
        }
        if ($user['role'] !== 'ADMIN' && $user['userId'] != $existing_cart['user_id']) {
            Flight::halt(403, 'Access Denied');
            return;
        }
        $updated_cart = $cart_service->update_cart($cart_id, $cart);
        if ($updated_cart) {
            Flight::json($updated_cart);
        } else {
            Flight::halt(400, 'Failed to update cart');
        }
    });


    /**
     * @OA\Delete(
     *     path="/carts/{cart_id}",
     *     summary="Delete an item from the cart",
     *     tags={"Carts"},
     *     security={{"ApiKey": {}}},
     *     @OA\Parameter(
     *         name="cart_id",
     *         in="path",
     *         required=true,
     *         description="Cart item ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cart item deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Cart successfully deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access denied"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cart item not found"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to delete cart item"
     *     )
     * )
     */
    Flight::route('DELETE /@cart_id', function ($cart_id) {
        $user = Flight::get('user');
        if (!$user || $user['userId'] == null) {
            Flight::halt(401, 'Unauthorized');
            return;
        }

        $cart_service = new CartService();
        $cart = $cart_service->get_cart_by_id($cart_id);
        if (!$cart) {
            Flight::halt(404, 'Cart not found');
            return;
        }

        if ($user['role'] !== 'ADMIN' && $user['userId'] != $cart['user_id']) {
            Flight::halt(403, 'Access Denied');
            return;
        }

        $success = $cart_service->delete_cart_by_id($cart_id);
        if ($success) {
            Flight::json(['success' => true, 'message' => 'Cart successfully deleted'], 200);
        } else {
            Flight::halt(400, 'Failed to delete cart');
        }
    });


    /**
     * @OA\Get(
     *     path="/carts/user/by_user_id",
     *     summary="Get all cart items for a specific user by user ID",
     *     tags={"Carts"},
     *     security={{"ApiKey": {}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="The ID of the user whose cart items are to be retrieved",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cart items retrieved successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/CartItem")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access denied"
     *     )
     * )
     */
    Flight::route('GET /user/by_user_id', function ($user_id) {
        $user = Flight::get('user');
        if ($user['role'] !== 'ADMIN' && $user['userId'] != $user_id) {
            Flight::halt(403, 'Access Denied');
        }
        $cart_service = new CartService();
        $carts = $cart_service->get_carts_by_user($user_id);
        Flight::json($carts);
    });
});
