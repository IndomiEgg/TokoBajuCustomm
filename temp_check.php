<?php
try {
    $db = new PDO("mysql:host=127.0.0.1;dbname=batom_studio;charset=utf8mb4","root","", array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    $stmt = $db->query("SELECT id, order_code, placement_location FROM orders ORDER BY id DESC LIMIT 5");
    foreach ($stmt as $row) {
        echo "ID=" . $row['id'] . " CODE=" . $row['order_code'] . " PL=" . $row['placement_location'] . "\n";
        $decoded = json_decode($row['placement_location'], true);
        echo "DECODE_TYPE=" . gettype($decoded) . " DECODE_VALUE=" . var_export($decoded, true) . "\n";
    }
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
