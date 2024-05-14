<?php

require_once __DIR__ . '/BaseDao.class.php';

class ProductDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct("products");
    }

    public function get_products($order = "-id")
    {
        list($order_column, $order_direction) = self::parse_order($order);
        $query = "SELECT *
                  FROM products
                  ORDER BY {$order_column} {$order_direction}";
        return $this->query($query, []);
    }

    public function get_product_by_id($product_id)
    {
        return $this->query_unique("SELECT * FROM products WHERE id = :id", ["id" => $product_id]);
    }

    public function add_product($product)
    {
        return $this->insert('products', $product);
    }

    public function update_product($product_id, $product)
    {
        return $this->execute_update('products', $product_id, $product);
    }

    public function delete_product_by_id($product_id)
    {
        return $this->delete('products', $product_id);
    }

    public function increment_product_bought($product_id)
    {
        $query = "UPDATE products SET times_bought = times_bought + 1 WHERE id = :id";
        $stmt = $this->connection->prepare($query);
        $stmt->execute(['id' => $product_id]);

        return $stmt->rowCount() > 0;
    }

    public function get_products_by_popularity()
    {
        $query = "SELECT * FROM products ORDER BY times_bought DESC";
        return $this->query($query, []);
    }
}
