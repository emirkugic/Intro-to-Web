<?php
require_once dirname(__FILE__) . "/../../config.php";

class BaseDao
{
  protected $connection;

  private $table;

  public function begin_transaction()
  {
    $response = $this->connection->beginTransaction();
  }

  public function commit()
  {
    $this->connection->commit();
  }

  public function rollback()
  {
    $response = $this->connection->rollBack();
  }

  protected function parse_order($order)
  {
    $order_direction = substr($order, 0, 1) == '-' ? 'DESC' : 'ASC';
    $order_column_raw = substr($order, 1);
    $order_column = trim($this->connection->quote($order_column_raw), "'");

    return [$order_column, $order_direction];
  }

  public function __construct($table)
  {
    $this->table = $table;
    try {
      $this->connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8;port=" . DB_PORT, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      ]);
    } catch (PDOException $e) {
      print_r($e);
      throw $e;
    }
  }

  public function insert($table, $entity)
  {
    $query = "INSERT INTO {$table} (";
    foreach ($entity as $column => $value) {
      $query .= $column . ", ";
    }
    $query = substr($query, 0, -2);
    $query .= ") VALUES (";
    foreach ($entity as $column => $value) {
      $query .= ":" . $column . ", ";
    }
    $query = substr($query, 0, -2);
    $query .= ")";

    $stmt = $this->connection->prepare($query);
    $stmt->execute($entity);
    $entity['id'] = $this->connection->lastInsertId();
    return $entity;
  }

  protected function execute_update($table, $id, $entity, $id_column = "id")
  {
    $query = "UPDATE {$table} SET ";
    foreach ($entity as $name => $value) {
      $query .= $name . "= :" . $name . ", ";
    }
    $query = substr($query, 0, -2);
    $query .= " WHERE {$id_column} = :id";

    $stmt = $this->connection->prepare($query);
    $entity['id'] = $id;
    $stmt->execute($entity);

    return $stmt->rowCount() > 0;
  }


  protected function query($query, $params)
  {
    $stmt = $this->connection->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  protected function query_unique($query, $params)
  {
    $results = $this->query($query, $params);
    return reset($results);
  }

  public function add($entity)
  {
    return $this->insert($this->table, $entity);
  }

  public function update($id, $entity)
  {
    $this->execute_update($this->table, $id, $entity);
  }

  public function get_by_id($id)
  {
    return $this->query_unique("SELECT * FROM " . $this->table . " WHERE id = :id", ["id" => $id]);
  }

  public function get_all($order = "-id")
  {
    list($order_column, $order_direction) = self::parse_order($order);
    return $this->query("SELECT * FROM " . $this->table . " ORDER BY {$order_column} {$order_direction}", []);
  }

  public function delete($table, $id, $id_column = "id")
  {
    $stmt = $this->connection->prepare("DELETE FROM {$table} WHERE {$id_column} = :id");
    $stmt->execute(['id' => $id]);

    return $stmt->rowCount() > 0;
  }
}
