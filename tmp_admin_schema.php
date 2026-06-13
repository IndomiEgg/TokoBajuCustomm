<?php
try {
    $db = new PDO('mysql:host=127.0.0.1;dbname=batom_studio;charset=utf8', 'root', '');
    echo "COLUMNS:\n";
    foreach ($db->query('DESCRIBE admin_users') as $row) {
        echo json_encode($row) . "\n";
    }
    echo "ROWS:\n";
    foreach ($db->query('SELECT id, email, password, account_status, full_name FROM admin_users LIMIT 20') as $row) {
        echo json_encode($row) . "\n";
    }
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
}
