<?php
try {
    $db = new PDO('mysql:host=127.0.0.1;dbname=batom_studio;charset=utf8', 'root', '');
    $password = 'admin123';
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $db->prepare('UPDATE admin_users SET password = ? WHERE email = ?');
    $stmt->execute([$hash, 'admin@batom.studio']);
    echo 'UPDATED admin@batom.studio with hash: ' . $hash . "\n";
    $stmt = $db->prepare('SELECT email,password FROM admin_users WHERE email = ?');
    $stmt->execute(['admin@batom.studio']);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($row, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    echo 'verify: ' . (password_verify($password, $row['password']) ? 'true' : 'false') . "\n";
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
}
