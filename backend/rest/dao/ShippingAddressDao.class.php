<?php

require_once __DIR__ . '/BaseDao.class.php';

class ShippingAddressDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct("shipping_addresses");
    }

    public function get_addresses_by_user($user_id, $order = "-id")
    {
        list($order_column, $order_direction) = self::parse_order($order);
        $query = "SELECT *
                  FROM shipping_addresses
                  WHERE user_id = :user_id
                  ORDER BY {$order_column} {$order_direction}";
        return $this->query($query, ["user_id" => $user_id]);
    }

    public function get_address_by_id($id)
    {
        return $this->query_unique("SELECT * FROM shipping_addresses WHERE id = :id", ["id" => $id]);
    }

    public function add_address($address)
    {
        return $this->insert('shipping_addresses', $address);
    }

    public function update_address($id, $address)
    {
        return $this->execute_update('shipping_addresses', $id, $address);
    }

    public function delete_address_by_id($id)
    {
        $this->delete('shipping_addresses', $id);
    }
}
