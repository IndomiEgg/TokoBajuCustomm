<?php
// Simple script to check users table

$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'batom_studio';

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, email, username, password FROM users LIMIT 10";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "=== Users in Database ===\n";
    while($row = $result->fetch_assoc()) {
        echo "ID: {$row['id']}\n";
        echo "Email: {$row['email']}\n";
        echo "Username: {$row['username']}\n";
        echo "Password (first 20 chars): " . substr($row['password'], 0, 20) . "...\n";
        echo "---\n";
    }
} else {
    echo "No users found in database\n";
}

$conn->close();
?>
