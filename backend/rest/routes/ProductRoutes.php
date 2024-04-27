<?php

require_once __DIR__ . '/../services/ProductService.class.php';

Flight::group('/products', function () {

    Flight::route('GET /', function () {
        $order = Flight::request()->query['order'] ?? '-id';
        $product_service = new ProductService();
        $products = $product_service->get_all_products($order);
        Flight::json($products, 201);
    });

    Flight::route('GET /popular', function () {
        $product_service = new ProductService();
        $products = $product_service->get_products_by_popularity();
        if ($products) {
            Flight::json($products, 201);
        } else {
            Flight::halt(404, 'No popular products found');
        }
    });


    Flight::route('GET /@id', function ($id) {
        $product_service = new ProductService();
        $product = $product_service->get_product_by_id($id);
        if ($product) {
            Flight::json($product, 201);
        } else {
            Flight::halt(404, 'Product not found');
        }
    });



    Flight::route('POST /add', function () {
        $data = Flight::request()->data->getData();
        $product_service = new ProductService();
        $product = $product_service->add_product($data);
        Flight::json($product, 201);
    });

    Flight::route('PUT /update/@id', function ($id) {
        $data = Flight::request()->data->getData();
        $product_service = new ProductService();
        $updated_product = $product_service->update_product($id, $data);
        if ($updated_product) {
            Flight::json(201);
        } else {
            Flight::halt(400, 'Failed to update product');
        }
    });

    Flight::route('DELETE /delete/@id', function ($id) {
        $product_service = new ProductService();
        $success = $product_service->delete_product_by_id($id);
        if ($success) {
            Flight::json(['message' => "Product successfully deleted"], 201);
        } else {
            Flight::halt(404, 'Product not found or could not be deleted');
        }
    });

    // TODO - doesn't work, is it needed?
    // Flight::route('POST /increment-bought/@id', function ($id) {
    //     $product_service = new ProductService();
    //     $success = $product_service->increment_product_bought($id);
    //     if ($success) {
    //         Flight::json(['message' => "Product bought count incremented"]);
    //     } else {
    //         Flight::halt(400, 'Failed to increment product bought count');
    //     }
    // });
});
