<?php
require 'vendor/autoload.php';
$db = \Config\Database::connect();
$fields = $db->getFieldData('users');

echo "Current users table columns:\n";
foreach($fields as $field) {
    echo "- {$field->name} ({$field->type})\n";
}
echo "\nAttempting to add missing columns...\n";

$existingColumns = array_column($fields, 'name');
print_r($existingColumns);
