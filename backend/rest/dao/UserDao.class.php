<?php

require_once __DIR__ . '/BaseDao.class.php';

class UserDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct("users");
    }

    public function get_user_by_id($id)
    {
        return $this->query_unique("SELECT * FROM users WHERE id = :id", ["id" => $id]);
    }

    public function get_all_users($offset = 0, $limit = 25, $order = "-id")
    {
        list($order_column, $order_direction) = self::parse_order($order);
        return $this->query("SELECT * FROM users ORDER BY {$order_column} {$order_direction} LIMIT {$limit} OFFSET {$offset}", []);
    }

    public function add_user($user)
    {
        return $this->insert('users', $user);
    }

    public function update_user($id, $user)
    {
        return $this->execute_update('users', $id, $user);
    }

    public function delete_user_by_id($id)
    {
        $this->execute("DELETE FROM users WHERE id = :id", ["id" => $id]);
    }


    public function get_user_by_email($email)
    {
        return $this->query_unique("SELECT * FROM users WHERE email = :email", ["email" => $email]);
    }
}
