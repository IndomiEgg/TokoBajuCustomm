<?php
try {
    $db = new PDO("mysql:host=127.0.0.1;dbname=batom_studio;charset=utf8mb4","root","", array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    $stmt = $db->query("SELECT id, placement_location FROM orders ORDER BY id DESC LIMIT 20");
    foreach ($stmt as $row) {
        $raw = $row['placement_location'];
        if (strpos($raw, '"[') === 0 && substr($raw, -2) === ']"') {
            echo "CANDIDATE ID=".$row['id']." RAW=".var_export($raw, true)."\n";
            $inner = json_decode($raw, false, 512, JSON_THROW_ON_ERROR);
            echo "INNER_TYPE=".gettype($inner)." INNER=".var_export($inner, true)."\n";
            if (is_string($inner)) {
                $fixed = $inner;
                $updated = $db->prepare("UPDATE orders SET placement_location = ? WHERE id = ?");
                $updated->execute([$fixed, $row['id']]);
                echo "FIXED ID=".$row['id']." NEW=".var_export($fixed, true)."\n";
            }
        }
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
