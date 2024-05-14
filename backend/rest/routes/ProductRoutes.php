<?php

require_once __DIR__ . '/../services/ProductService.class.php';
require_once __DIR__ . '/../../middleware.php';



/**
 * @OA\Schema(
 *     schema="Product",
 *     required={"title", "price", "quantity", "image_url"},
 *     @OA\Property(property="title", type="string", description="The title of the product"),
 *     @OA\Property(property="price", type="number", format="float", description="The price of the product"),
 *     @OA\Property(property="quantity", type="integer", description="Available quantity of the product"),
 *     @OA\Property(property="image_url", type="string", description="URL of the product image"),
 *     @OA\Property(property="times_bought", type="integer", description="The number of times the product has been purchased")
 * )
 */
Flight::group('/products', function () {


    /**
     * @OA\Get(
     *     path="/products/all",
     *     summary="List all products",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="order",
     *         in="query",
     *         description="Ordering of returned products",
     *         required=false,
     *         @OA\Schema(type="string", example="-id")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of products",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Product")
     *         )
     *     )
     * )
     */
    Flight::route('GET /all', function () {
        $order = Flight::request()->query['order'] ?? '-id';
        $product_service = new ProductService();
        $products = $product_service->get_all_products($order);
        Flight::json($products);
    });


    /**
     * @OA\Get(
     *     path="/products/popular",
     *     summary="List popular products",
     *     tags={"Products"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of popular products",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Product")
     *         )
     *     )
     * )
     */
    Flight::route('GET /popular', function () {
        $product_service = new ProductService();
        $products = $product_service->get_products_by_popularity();
        Flight::json($products);
    });


    /**
     * @OA\Get(
     *     path="/products/{id}",
     *     summary="Get a product by its ID",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of product",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    Flight::route('GET /@id', function ($id) {
        $product_service = new ProductService();
        $product = $product_service->get_product_by_id($id);
        if ($product) {
            Flight::json($product);
        } else {
            Flight::halt(404, 'Product not found');
        }
    });


    /**
     * @OA\Post(
     *     path="/products/add",
     *     summary="Add a new product",
     *     tags={"Products"},
     *     security={{"ApiKey": {}}},
     *     @OA\RequestBody(
     *         description="Product data to add",
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"title", "price", "quantity", "image_url"},
     *             @OA\Property(property="title", type="string", example="Comfy Couch"),
     *             @OA\Property(property="price", type="number", format="float", example=599.99),
     *             @OA\Property(property="quantity", type="integer", example=1),
     *             @OA\Property(property="image_url", type="string", example="https://i5.walmartimages.com/seo/HONBAY-Convertible-Sectional-Sofa-Couch-L-Shaped-Couch-with-Modern-Linen-Fabric-for-Small-Space-Dark-Grey_8fa9e00b-da00-471a-877d-cd75bc6a99d3.7fe1377ac59b79930622ead1930224d4.jpeg"),
     *             @OA\Property(property="times_bought", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product successfully added",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input, product not created"
     *     )
     * )
     */
    Flight::route('POST /add', function () {
        authorize("ADMIN");
        $data = Flight::request()->data->getData();
        $product_service = new ProductService();
        $product = $product_service->add_product($data);
        Flight::json($product, 201);
    });


    /**
     * @OA\Put(
     *     path="/products/update/{id}",
     *     summary="Update an existing product",
     *     tags={"Products"},
     *     security={{"ApiKey": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         description="Product data to update",
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="title", type="string", example="Updated Comfy Couch"),
     *             @OA\Property(property="price", type="number", format="float", example=650.00),
     *             @OA\Property(property="quantity", type="integer", example=5),
     *             @OA\Property(property="image_url", type="string", example="https://newimageurl.com/updated.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product successfully updated",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input, product not updated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    Flight::route('PUT /update/@id', function ($id) {
        authorize("ADMIN");
        $data = Flight::request()->data->getData();
        $product_service = new ProductService();
        $updated_product = $product_service->update_product($id, $data);
        if ($updated_product) {
            Flight::json($updated_product, 201);
        } else {
            Flight::halt(400, 'Failed to update product');
        }
    });


    /**
     * @OA\Delete(
     *     path="/products/delete/{id}",
     *     summary="Delete a product by ID",
     *     tags={"Products"},
     *     security={{"ApiKey": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product successfully deleted",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Product successfully deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found or could not be deleted"
     *     )
     * )
     */
    Flight::route('DELETE /delete/@id', function ($id) {
        authorize("ADMIN");
        $product_service = new ProductService();
        $success = $product_service->delete_product_by_id($id);
        if ($success) {
            Flight::json(['message' => "Product successfully deleted"], 201);
        } else {
            Flight::halt(404, 'Product not found or could not be deleted');
        }
    });
});
