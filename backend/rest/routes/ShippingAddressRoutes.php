<?php

require_once __DIR__ . '/../services/ShippingAddressService.class.php';
require_once __DIR__ . '/../../middleware.php';



/**
 * @OA\Schema(
 *     schema="ShippingAddress",
 *     type="object",
 *     required={"user_id", "country", "state", "city", "zip_code", "address"},
 *     @OA\Property(property="id", type="integer", description="Unique identifier for the shipping address"),
 *     @OA\Property(property="user_id", type="integer", description="Identifier for the user who owns this address"),
 *     @OA\Property(property="country", type="string", description="Country part of the address"),
 *     @OA\Property(property="state", type="string", description="State part of the address"),
 *     @OA\Property(property="city", type="string", description="City part of the address"),
 *     @OA\Property(property="zip_code", type="string", description="Postal code of the address"),
 *     @OA\Property(property="address", type="string", description="Street address"),
 *     @OA\Property(property="apartment_suite_unit", type="string", description="Apartment, suite, or unit number"),
 *     @OA\Property(property="company_name", type="string", description="Company name, if applicable")
 * )
 */
Flight::group('/shipping-addresses', function () {

    /**
     * @OA\Get(
     *     path="/shipping-addresses/full-address",
     *     summary="Get full shipping address for the logged-in user",
     *     tags={"Shipping Address"},
     *     security={{"ApiKey": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of the full address",
     *         @OA\JsonContent(ref="#/components/schemas/ShippingAddress")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access denied"
     *     )
     * )
     */
    Flight::route('GET /full-address', function () {
        $user = Flight::get('user');
        $user_id = $user['userId'];

        $shipping_address_service = new ShippingAddressService();
        $address = $shipping_address_service->get_full_address($user_id);
        Flight::json($address);
    });


    /**
     * @OA\Get(
     *     path="/shipping-addresses/user/{user_id}",
     *     summary="Get all shipping addresses for a specific user",
     *     tags={"Shipping Address"},
     *     security={{"ApiKey": {}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="User ID whose addresses are to be fetched",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of addresses",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ShippingAddress")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access denied"
     *     )
     * )
     */
    Flight::route('GET /user/@user_id', function ($user_id) {
        $user = Flight::get('user');
        if ($user['role'] !== 'USER' && $user['userId'] != $user_id) {
            Flight::halt(403, 'Access Denied');
            return;
        }
        $shipping_address_service = new ShippingAddressService();
        $addresses = $shipping_address_service->get_all_addresses($user_id);
        Flight::json($addresses);
    });


    /**
     * @OA\Get(
     *     path="/shipping-addresses/{id}",
     *     summary="Get a specific shipping address by ID",
     *     tags={"Shipping Address"},
     *     security={{"ApiKey": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Shipping address ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of address",
     *         @OA\JsonContent(ref="#/components/schemas/ShippingAddress")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access denied"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Address not found"
     *     )
     * )
     */
    Flight::route('GET /@id', function ($id) {
        $user = Flight::get('user');
        $shipping_address_service = new ShippingAddressService();
        $address = $shipping_address_service->get_address_by_id($id);
        if (!$address) {
            Flight::halt(404, 'Address not found');
            return;
        }
        if ($user['role'] !== 'ADMIN' && $user['userId'] != $address['user_id']) {
            Flight::halt(403, 'Access Denied');
            return;
        }
        Flight::json($address);
    });


    /**
     * @OA\Post(
     *     path="/shipping-addresses/",
     *     summary="Add a new shipping address",
     *     tags={"Shipping Address"},
     *     security={{"ApiKey": {}}},
     *     @OA\RequestBody(
     *         description="Data for the new shipping address",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ShippingAddress")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Address successfully added",
     *         @OA\JsonContent(ref="#/components/schemas/ShippingAddress")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to add address"
     *     )
     * )
     */
    Flight::route('POST /', function () {
        $user = Flight::get('user');
        $address = Flight::request()->data->getData();
        $address['user_id'] = $user['userId'];
        $shipping_address_service = new ShippingAddressService();
        $result = $shipping_address_service->add_address($address);
        if ($result) {
            Flight::json($result, 201);
        } else {
            Flight::halt(400, 'Failed to add address');
        }
    });


    /**
     * @OA\Put(
     *     path="/shipping-addresses/{id}",
     *     summary="Update an existing shipping address",
     *     tags={"Shipping Address"},
     *     security={{"ApiKey": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Shipping address ID to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         description="Data for updating the shipping address",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ShippingAddress")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Address successfully updated",
     *         @OA\JsonContent(ref="#/components/schemas/ShippingAddress")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to update address"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Address not found"
     *     )
     * )
     */
    Flight::route('PUT /@id', function ($id) {
        $user = Flight::get('user');
        $shipping_address_service = new ShippingAddressService();
        $address = Flight::request()->data->getData();
        $existing_address = $shipping_address_service->get_address_by_id($id);
        if (!$existing_address) {
            Flight::halt(404, 'Address not found');
            return;
        }
        if ($user['role'] !== 'ADMIN' && $user['userId'] != $existing_address['user_id']) {
            Flight::halt(403, 'Access Denied');
            return;
        }
        $updated_address = $shipping_address_service->update_address($id, $address);
        if ($updated_address) {
            Flight::json($updated_address);
        } else {
            Flight::halt(400, 'Failed to update address');
        }
    });


    /**
     * @OA\Delete(
     *     path="/shipping-addresses/{id}",
     *     summary="Delete a shipping address",
     *     tags={"Shipping Address"},
     *     security={{"ApiKey": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Shipping address ID to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Address successfully deleted",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Address successfully deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to delete address"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Address not found"
     *     )
     * )
     */
    Flight::route('DELETE /@id', function ($id) {
        $user = Flight::get('user');
        $shipping_address_service = new ShippingAddressService();
        $address = $shipping_address_service->get_address_by_id($id);
        if (!$address) {
            Flight::halt(404, 'Address not found');
            return;
        }
        if ($user['role'] !== 'ADMIN' && $user['userId'] != $address['user_id']) {
            Flight::halt(403, 'Access Denied');
            return;
        }
        $success = $shipping_address_service->delete_address_by_id($id);
        if ($success) {
            Flight::json(['message' => 'Address successfully deleted'], 200);
        } else {
            Flight::halt(400, 'Failed to delete address');
        }
    });
});
