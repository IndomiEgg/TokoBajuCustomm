<?php
try {
 $db = new PDO("mysql:host=127.0.0.1;dbname=batom_studio;charset=utf8mb4","root","", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
 $stmt = $db->query("SHOW FULL COLUMNS FROM orders");
 foreach($stmt as $row) {
   echo $row["Field"] . " " . $row["Type"] . " " . $row["Null"] . " " . ($row["Default"] === null ? "NULL" : $row["Default"]) . "\n";
 }
} catch (PDOException $e) {
 echo "ERROR: " . $e->getMessage();
}
