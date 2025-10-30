<?php
namespace App\Models;

use App\Database;
use PDO;

class Customer
{
    // Liệt kê khách hàng dựa trên đơn hàng (gom theo phone)
    public static function all(): array
    {
        $pdo = Database::getInstance()->pdo();
        $sql = "SELECT 
                    MIN(id) AS any_id,
                    customer_name,
                    phone,
                    MAX(address) AS address,
                    COUNT(*) AS orders_count,
                    SUM(total) AS total_spent,
                    MIN(created_at) AS first_order,
                    MAX(created_at) AS last_order
                FROM orders
                GROUP BY phone, customer_name
                ORDER BY last_order DESC";
        $stmt = $pdo->query($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public static function ordersByPhone(string $phone): array
    {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->prepare('SELECT * FROM orders WHERE phone = :p ORDER BY id DESC');
        $stmt->execute([':p'=>$phone]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

