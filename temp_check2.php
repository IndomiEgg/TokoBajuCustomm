<?php
try {
    $db = new PDO("mysql:host=127.0.0.1;dbname=batom_studio;charset=utf8mb4","root","", array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    $stmt = $db->query("SELECT id, order_code, placement_location, CHAR_LENGTH(placement_location) AS len FROM orders ORDER BY id DESC LIMIT 10");
    foreach ($stmt as $row) {
        echo "ID=".$row['id']." CODE=".$row['order_code']." LEN=".$row['len']."\n";
        echo "RAW=".var_export($row['placement_location'], true)."\n";
        $decoded = null;
        try {
            $decoded = json_decode($row['placement_location'], false, 512, JSON_THROW_ON_ERROR);
            echo "DECODE_TYPE=".gettype($decoded)."\n";
            if (is_array($decoded) || is_object($decoded)) {
                echo "DECODE_OK\n";
            } else {
                echo "DECODE_VALUE=".var_export($decoded, true)."\n";
            }
        } catch (Exception $e) {
            echo "DECODE_ERROR=".$e->getMessage()."\n";
        }
        echo "---\n";
    }
} catch (PDOException $e) { echo "ERROR: " . $e->getMessage(); }
