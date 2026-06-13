<?php
$db = new PDO('mysql:host=127.0.0.1;dbname=toko_baju_custom;charset=utf8','root','');
$stmt = $db->prepare('SELECT id, email, password, is_active FROM admin_users WHERE email = ?');
$stmt->execute([ $argv[1] ?? 'admin@batom.local' ]);
$r = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode($r, JSON_PRETTY_PRINT) . PHP_EOL;
