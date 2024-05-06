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
        return $this->delete('shipping_addresses', $id);
    }


    /*
    SELECT sa.country, u.first_name, u.last_name, sa.company_name, sa.address, sa.state, sa.zip_code, u.email, u.phone
    FROM shipping_addresses sa 
    JOIN users u ON u.id = sa.user_id
    WHERE u.id = 2
    */

    public function get_full_address($user_id)
    {
        $query = "SELECT  sa.country, u.first_name, u.last_name, sa.company_name, sa.address, sa.state, sa.zip_code, u.email, u.phone
                  FROM shipping_addresses sa 
                  JOIN users u ON u.id = sa.user_id
                  WHERE u.id = :user_id";
        return $this->query($query, ["user_id" => $user_id]);
    }
}
