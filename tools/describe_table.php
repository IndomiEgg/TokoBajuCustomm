<?php
$db = new PDO('mysql:host=127.0.0.1;dbname=toko_baju_custom;charset=utf8','root','');
$stmt = $db->query('SHOW COLUMNS FROM admin_users');
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($cols, JSON_PRETTY_PRINT) . PHP_EOL;
