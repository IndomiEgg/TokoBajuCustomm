<?php
try {
    $db = new PDO("mysql:host=127.0.0.1;dbname=batom_studio;charset=utf8mb4","root","", array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    $stmt = $db->query("SELECT id, placement_location FROM orders ORDER BY id DESC LIMIT 50");
    foreach ($stmt as $row) {
        $raw = $row['placement_location'];
        $len = strlen($raw);
        $needsFix = false;
        if ($len >= 2 && $raw[0] === '"' && $raw[$len-1] === '"') {
            try {
                $inner = json_decode($raw, false, 512, JSON_THROW_ON_ERROR);
                if (is_string($inner)) {
                    $decoded = json_decode($inner, false, 512, JSON_THROW_ON_ERROR);
                    if (is_array($decoded) || is_object($decoded)) {
                        $needsFix = true;
                        $fixed = $inner;
                    }
                }
            } catch (Exception $e) {
                // ignore
            }
        }
        if ($needsFix) {
            $updated = $db->prepare("UPDATE orders SET placement_location = ? WHERE id = ?");
            $updated->execute([$fixed, $row['id']]);
            echo "FIXED id={$row['id']} raw=" . var_export($raw, true) . " new=" . var_export($fixed, true) . "\n";
        }
    }
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
