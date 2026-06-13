<?php
try {
    $db = new PDO('mysql:host=127.0.0.1;dbname=batom_studio;charset=utf8', 'root', '');
    $stmt = $db->query('SELECT id, email, password, is_active, account_status, full_name FROM admin_users LIMIT 20');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
}
