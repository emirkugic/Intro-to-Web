<?php
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/../rest/services/ProductService.class.php';

$product_service = new ProductService();

$popular_products = $product_service->get_products_by_popularity(0, 3);

header('Content-Type: application/json');
echo json_encode($popular_products);
