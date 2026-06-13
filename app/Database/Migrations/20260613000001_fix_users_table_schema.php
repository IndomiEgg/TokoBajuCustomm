<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixUsersTableSchema extends Migration
{
    public function up()
    {
        // Check if columns already exist and add only missing ones
        $db = \Config\Database::connect();
        $fields = $db->getFieldData('users');
        $existingColumns = array_column($fields, 'name');

        $columnsToAdd = [];

        if (!in_array('full_name', $existingColumns)) {
            $columnsToAdd['full_name'] = [
                'type'       => 'VARCHAR',
                'constraint' => '120',
                'null'       => true,
            ];
        }

        if (!in_array('phone_number', $existingColumns)) {
            $columnsToAdd['phone_number'] = [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ];
        }

        if (!in_array('profile_picture', $existingColumns)) {
            $columnsToAdd['profile_picture'] = [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ];
        }

        if (!in_array('address', $existingColumns)) {
            $columnsToAdd['address'] = [
                'type'       => 'TEXT',
                'null'       => true,
            ];
        }

        if (!in_array('city', $existingColumns)) {
            $columnsToAdd['city'] = [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ];
        }

        if (!in_array('postal_code', $existingColumns)) {
            $columnsToAdd['postal_code'] = [
                'type'       => 'VARCHAR',
                'constraint' => '10',
                'null'       => true,
            ];
        }

        if (!in_array('country', $existingColumns)) {
            $columnsToAdd['country'] = [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ];
        }

        if (!in_array('account_status', $existingColumns)) {
            $columnsToAdd['account_status'] = [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => 'active',
            ];
        }

        if (!in_array('verification_token', $existingColumns)) {
            $columnsToAdd['verification_token'] = [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ];
        }

        if (!in_array('is_verified', $existingColumns)) {
            $columnsToAdd['is_verified'] = [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ];
        }

        if (!in_array('whatsapp_verified', $existingColumns)) {
            $columnsToAdd['whatsapp_verified'] = [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ];
        }

        if (!in_array('last_login', $existingColumns)) {
            $columnsToAdd['last_login'] = [
                'type'       => 'DATETIME',
                'null'       => true,
            ];
        }

        // Add only missing columns
        if (!empty($columnsToAdd)) {
            $this->forge->addColumn('users', $columnsToAdd);
        }
    }

    public function down()
    {
        $columnsToRemove = [
            'full_name',
            'phone_number',
            'profile_picture',
            'address',
            'city',
            'postal_code',
            'country',
            'account_status',
            'verification_token',
            'is_verified',
            'whatsapp_verified',
            'last_login',
        ];

        $this->forge->dropColumn('users', $columnsToRemove);
    }
}
