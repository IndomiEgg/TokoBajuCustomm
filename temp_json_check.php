<?php
try {
    $db = new PDO("mysql:host=127.0.0.1;dbname=batom_studio;charset=utf8mb4","root","", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $stmt = $db->query("SELECT id, order_code, placement_location, JSON_VALID(placement_location) AS valid, JSON_TYPE(placement_location) AS type FROM orders ORDER BY id DESC LIMIT 100");
    foreach ($stmt as $row) {
        if ($row['valid'] != 1 || $row['type'] !== 'ARRAY') {
            echo sprintf("ID=%s CODE=%s VALID=%s TYPE=%s RAW=%s\n", $row['id'], $row['order_code'], $row['valid'], $row['type'], var_export($row['placement_location'], true));
        }
    }
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
