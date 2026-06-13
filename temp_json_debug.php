<?php
try {
    $db = new PDO("mysql:host=127.0.0.1;dbname=batom_studio;charset=utf8mb4","root","", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $stmt = $db->query("SELECT id, order_code, placement_location, JSON_VALID(placement_location) AS valid, JSON_TYPE(placement_location) AS type, LEFT(placement_location,1) AS first FROM orders ORDER BY id DESC LIMIT 20");
    foreach ($stmt as $row) {
        echo "ID={$row['id']} CODE={$row['order_code']} VALID={$row['valid']} TYPE={$row['type']} FIRST={$row['first']} RAW=" . var_export($row['placement_location'], true) . "\n";
    }
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
