<?php

namespace EmailQueue\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQueueTable extends Migration
{
    public function up()
    {

        $this->forge->addField([
              'id'      => [
              'type'       => 'INT',
              'constraint'     => 11,
              'unsigned'       => true,
              'auto_increment' => true,
            ],
            'email'        => [
                'type'       => 'varchar',
                'constraint' => 120,
            ],
            'from_name'      => [
              'type'       => 'varchar',
              'constraint' => 255,
            ],
            'from_email'       => [
                'type'       => 'varchar',
                'constraint' => 255,
            ],
            'subject'       => [
                'type'       => 'varchar',
                'constraint' => 255,
            ],
            'message'       => [
                'type'       => 'TEXT',
            ],
            'attempts' => [
                'type' => 'INT',
                'constraint' => 2,
                'null' => false,
            ],
            'sent' => [
                'type' => 'tinyint',
                'constraint' => 1,
                'default' => 0,
            ],
            'sent_at' => [
                'type' => 'datetime',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'datetime',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'datetime',
                'null' => false,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('email_queue', true);

    }

    public function down()
    {
        $this->forge->dropTable('email_queue');
    }
}
