<?php
try {
    $db = new PDO("mysql:host=127.0.0.1;dbname=batom_studio;charset=utf8mb4","root","", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $sql = "SELECT id, order_code, placement_location, JSON_TYPE(placement_location) AS tp, JSON_VALID(placement_location) AS valid FROM orders WHERE placement_location LIKE '"%\\\"%''";
    $stmt = $db->query($sql);
    foreach ($stmt as $row) {
        echo "ID={$row['id']} CODE={$row['order_code']} TP={$row['tp']} VALID={$row['valid']} RAW=" . var_export($row['placement_location'], true) . "\n";
    }
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
