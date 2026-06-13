<?php
try {
    $db = new PDO("mysql:host=127.0.0.1;dbname=batom_studio;charset=utf8mb4","root","", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $stmt = $db->query("SELECT id, order_code, placement_location, HEX(placement_location) AS hexval FROM orders ORDER BY id DESC");
    foreach ($stmt as $row) {
        echo "ID={$row['id']} CODE={$row['order_code']} HEX={$row['hexval']} RAW=" . var_export($row['placement_location'], true) . "\n";
    }
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
