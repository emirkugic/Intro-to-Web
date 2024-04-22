<?php
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/../../rest/services/ProductService.class.php';

$product_service = new ProductService();

$offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
$limit = isset($_GET['limit']) ? $_GET['limit'] : 25;
$order = isset($_GET['order']) ? $_GET['order'] : "-id";

$products = $product_service->get_all_products($offset, $limit, $order);

header('Content-Type: application/json');
echo json_encode($products);
