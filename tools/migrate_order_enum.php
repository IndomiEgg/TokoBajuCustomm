<?php
try {
    $db = new PDO('mysql:host=127.0.0.1;dbname=batom_studio;charset=utf8', 'root', '', [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    $sql1 = "ALTER TABLE `orders` MODIFY `order_status` ENUM('pending','approved','in_progress','processing','delivering','finished','quality_check','ready_to_ship','shipped','delivered','cancelled') DEFAULT 'pending'";
    $sql2 = "ALTER TABLE `order_status_logs` MODIFY `status_type` ENUM('pending','approved','in_progress','processing','delivering','finished','quality_check','ready_to_ship','shipped','delivered','cancelled') NOT NULL";

    echo "Running: $sql1\n";
    $db->exec($sql1);
    echo "orders.order_status updated\n";

    echo "Running: $sql2\n";
    $db->exec($sql2);
    echo "order_status_logs.status_type updated\n";

} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
}
