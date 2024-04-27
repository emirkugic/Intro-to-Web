<?php

require_once __DIR__ . '/../dao/CartDao.class.php';

class CartService
{
    private $cart_dao;

    public function __construct()
    {
        $this->cart_dao = new CartDao();
    }

    public function get_all_carts($offset = 0, $limit = 25, $order = "-id")
    {
        return $this->cart_dao->get_carts($offset, $limit, $order);
    }

    public function get_cart_by_id($cart_id)
    {
        return $this->cart_dao->get_cart_by_id($cart_id);
    }

    public function add_cart($cart)
    {
        return $this->cart_dao->add_cart($cart);
    }

    public function update_cart($cart_id, $cart)
    {
        return $this->cart_dao->update_cart($cart_id, $cart);
    }

    public function delete_cart_by_id($cart_id)
    {
        return $this->cart_dao->delete_cart_by_id($cart_id);
    }

    public function get_carts_by_user($user_id)
    {
        return $this->cart_dao->get_carts_by_user($user_id);
    }

    public function
    update_cart_quantity($cart_id, $quantity)
    {
        return $this->cart_dao->update_cart_quantity($cart_id, $quantity);
    }
}
