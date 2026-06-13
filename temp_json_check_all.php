<?php
try {
    $db = new PDO("mysql:host=127.0.0.1;dbname=batom_studio;charset=utf8mb4","root","", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $stmt = $db->query("SELECT id, order_code, placement_location FROM orders");
    $bad = 0;
    foreach ($stmt as $row) {
        $raw = $row['placement_location'];
        if ($raw === null || $raw === '') {
            echo "ID={$row['id']} CODE={$row['order_code']} EMPTY\n";
            $bad++;
            continue;
        }
        try {
            $decoded = json_decode($raw, false, 512, JSON_THROW_ON_ERROR);
            if (is_string($decoded)) {
                echo "ID={$row['id']} CODE={$row['order_code']} STRING_DECODE raw=" . var_export($raw, true) . "\n";
                $bad++;
            } elseif (!is_array($decoded) && !is_object($decoded)) {
                echo "ID={$row['id']} CODE={$row['order_code']} OTHER_DECODE type=".gettype($decoded)." raw=" . var_export($raw, true) . "\n";
                $bad++;
            }
        } catch (Exception $e) {
            echo "ID={$row['id']} CODE={$row['order_code']} JSON_ERROR=".$e->getMessage()." raw=" . var_export($raw, true) . "\n";
            $bad++;
        }
    }
    echo "TOTAL_BAD={$bad}\n";
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
