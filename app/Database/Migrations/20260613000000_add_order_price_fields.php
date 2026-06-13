<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOrderPriceFields extends Migration
{
    public function up()
    {
        $fields = [
            'final_price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'default' => null,
            ],
            'price_confirmed' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'price_note' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'whatsapp_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
        ];

        $this->forge->addColumn('orders', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('orders', 'final_price');
        $this->forge->dropColumn('orders', 'price_confirmed');
        $this->forge->dropColumn('orders', 'price_note');
        $this->forge->dropColumn('orders', 'whatsapp_url');
    }
}
