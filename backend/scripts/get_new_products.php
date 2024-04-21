<?php
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../rest/services/ProductService.class.php';

$product_service = new ProductService();

$newest_products = $product_service->get_all_products(0, 3, "-id");

header('Content-Type: application/json');
echo json_encode($newest_products);
