<?php

require_once __DIR__ . '/../services/ProductService.class.php';
require_once __DIR__ . '/../../middleware.php';

Flight::group('/products', function () {
    Flight::route('GET /', function () {
        $order = Flight::request()->query['order'] ?? '-id';
        $product_service = new ProductService();
        $products = $product_service->get_all_products($order);
        Flight::json($products);
    });

    Flight::route('GET /popular', function () {
        $product_service = new ProductService();
        $products = $product_service->get_products_by_popularity();
        Flight::json($products);
    });

    Flight::route('GET /@id', function ($id) {
        $product_service = new ProductService();
        $product = $product_service->get_product_by_id($id);
        if ($product) {
            Flight::json($product);
        } else {
            Flight::halt(404, 'Product not found');
        }
    });

    Flight::route('POST /add', function () {
        authorize("ADMIN");
        $data = Flight::request()->data->getData();
        $product_service = new ProductService();
        $product = $product_service->add_product($data);
        Flight::json($product, 201);
    });

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
