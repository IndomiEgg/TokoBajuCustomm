<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOrderPriceFields extends Migration
{
    public function up()
    {
        // Check if columns already exist
        $db = \Config\Database::connect();
        $fields = $db->getFieldData('orders');
        $existingColumns = array_column($fields, 'name');

        $fieldsToAdd = [];

        if (!in_array('final_price', $existingColumns)) {
            $fieldsToAdd['final_price'] = [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'default' => null,
            ];
        }

        if (!in_array('price_confirmed', $existingColumns)) {
            $fieldsToAdd['price_confirmed'] = [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ];
        }

        if (!in_array('price_note', $existingColumns)) {
            $fieldsToAdd['price_note'] = [
                'type' => 'TEXT',
                'null' => true,
            ];
        }

        if (!in_array('whatsapp_url', $existingColumns)) {
            $fieldsToAdd['whatsapp_url'] = [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ];
        }

        // Only add fields that don't exist
        if (!empty($fieldsToAdd)) {
            $this->forge->addColumn('orders', $fieldsToAdd);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('orders', 'final_price');
        $this->forge->dropColumn('orders', 'price_confirmed');
        $this->forge->dropColumn('orders', 'price_note');
        $this->forge->dropColumn('orders', 'whatsapp_url');
    }
}
