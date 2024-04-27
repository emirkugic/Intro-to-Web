<?php

require_once __DIR__ . '/../dao/ProductDao.class.php';

class ProductService
{

    private $product_dao;

    public function __construct()
    {
        $this->product_dao = new ProductDao();
    }


    public function get_all_products($order = "-id")
    {
        return $this->product_dao->get_products($order);
    }


    public function get_product_by_id($product_id)
    {
        return $this->product_dao->get_product_by_id($product_id);
    }


    public function add_product($product)
    {
        return $this->product_dao->add_product($product);
    }


    public function update_product($product_id, $product)
    {
        return $this->product_dao->update_product($product_id, $product);
    }


    public function delete_product_by_id($product_id)
    {
        return $this->product_dao->delete_product_by_id($product_id);
    }


    public function increment_product_bought($product_id)
    {
        return $this->product_dao->increment_product_bought($product_id);
    }


    public function get_products_by_popularity()
    {
        return $this->product_dao->get_products_by_popularity();
    }
}
