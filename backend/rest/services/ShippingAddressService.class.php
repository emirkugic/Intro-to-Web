<?php

require_once __DIR__ . '/../dao/ShippingAddressDao.class.php';

class ShippingAddressService
{

    private $shipping_address_dao;

    public function __construct()
    {
        $this->shipping_address_dao = new ShippingAddressDao();
    }

    public function get_all_addresses($user_id, $offset = 0, $limit = 25, $order = "-id")
    {
        return $this->shipping_address_dao->get_addresses_by_user($user_id, $offset, $limit, $order);
    }

    public function get_address_by_id($id)
    {
        return $this->shipping_address_dao->get_address_by_id($id);
    }

    public function add_address($address)
    {
        return $this->shipping_address_dao->add_address($address);
    }

    public function update_address($id, $address)
    {
        return $this->shipping_address_dao->update_address($id, $address);
    }

    public function delete_address_by_id($id)
    {
        return $this->shipping_address_dao->delete_address_by_id($id);
    }
}
