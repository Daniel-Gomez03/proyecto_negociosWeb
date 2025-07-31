<?php
namespace Dao\Orders;

use Dao\Table;

class Order extends Table
{
    public static function insertOrder($orderId, $userId, $total, $status, $paypalData)
    {
        $sql = "INSERT INTO orders (order_id, user_id, total, status, paypal_data) 
                VALUES (:order_id, :user_id, :total, :status, :paypal_data)";

        $params = [
            "order_id" => $orderId,
            "user_id" => $userId,
            "total" => $total,
            "status" => $status,
            "paypal_data" => json_encode($paypalData)
        ];

        $inserted = self::executeNonQuery($sql, $params);

        if ($inserted) {
            return self::getLastInsertId();
        }
        return false;
    }

    private static function getLastInsertId()
    {
        $sql = "SELECT LAST_INSERT_ID() as lastId";
        $row = self::obtenerUnRegistro($sql, []);
        return $row['lastId'] ?? null;
    }

    public static function getOrdersByUserId(int $userId): array
    {
        $sql = "SELECT order_id, total, status, created_at 
                FROM orders 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC";

        return self::obtenerRegistros($sql, ["user_id" => $userId]);
    }

    public static function getOrderByUserAndId(string $orderId, int $userId)
    {
        $sql = "SELECT * FROM orders 
                WHERE order_id = :order_id AND user_id = :user_id 
                LIMIT 1";

        $params = [
            "order_id" => $orderId,
            "user_id" => $userId
        ];

        return self::obtenerUnRegistro($sql, $params);
    }

    public static function insertOrderDetail(int $orderId, array $item)
    {
        $subtotal = floatval($item['quantity']) * floatval($item['price']);

        $sql = "INSERT INTO order_details (orderId, productId, quantity, price, subtotal)
            VALUES (:orderId, :productId, :quantity, :price, :subtotal)";

        $params = [
            "orderId" => $orderId,
            "productId" => $item['productId'],
            "quantity" => $item['quantity'],
            "price" => $item['price'],
            "subtotal" => $subtotal
        ];

        return self::executeNonQuery($sql, $params);
    }

    public static function getOrderDetails(int $orderId)
    {
        $sql = "SELECT d.*, p.productName, p.productDescription
                FROM order_details d
                JOIN products p ON d.productId = p.productId
                WHERE d.orderId = :orderId";

        return self::obtenerRegistros($sql, ["orderId" => $orderId]);
    }
}