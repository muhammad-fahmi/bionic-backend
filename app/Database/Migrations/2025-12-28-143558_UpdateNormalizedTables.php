<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateNormalizedTables extends Migration
{
    public function up()
    {
        // Drop old tables if they exist (in reverse dependency order)
        $this->forge->dropTable('r_task_submission_actions', true);
        $this->forge->dropTable('r_task_submission_items', true);
        $this->forge->dropTable('r_task_submissions', true);
        $this->forge->dropTable('m_actions', true);
        $this->forge->dropTable('m_items', true);
        $this->forge->dropTable('r_shift_assignments', true);
        $this->forge->dropTable('r_shifts', true);
        $this->forge->dropTable('m_locations', true);
        $this->forge->dropTable('m_users', true);

        // Create m_users table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'jabatan' => [
                'type' => 'ENUM',
                'constraint' => ['admin', 'petugas', 'verifikator'],
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('m_users');

        // Create m_locations table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'shift' => [
                'type' => 'INT',
                'constraint' => 1,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('m_locations');

        // Create m_items table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'nama_display' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['complete_task', 'additional_task'],
                'default' => 'complete_task',
            ],
            'lokasi_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('lokasi_id', 'm_locations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('m_items');

        // Create m_actions table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nama_aksi' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'nama_aksi_display' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'nama_item' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('m_actions');

        // Create r_shifts table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'shift_code' => [
                'type' => 'INT',
                'constraint' => 1,
            ],
            'date' => [
                'type' => 'DATE',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'm_users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('r_shifts');

        // Create r_shift_assignments table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'shift_code' => [
                'type' => 'INT',
                'constraint' => 1,
            ],
            'start_date' => [
                'type' => 'DATE',
            ],
            'end_date' => [
                'type' => 'DATE',
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'm_users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('r_shift_assignments');

        // Create r_task_submissions table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'tanggal' => [
                'type' => 'DATETIME',
            ],
            'petugas' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'shift' => [
                'type' => 'INT',
                'constraint' => 1,
            ],
            'lokasi_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default' => 'pending',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('lokasi_id', 'm_locations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('r_task_submissions');

        // Create r_task_submission_items table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'task_submission_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'item_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'kondisi' => [
                'type' => 'ENUM',
                'constraint' => ['dibersihkan', 'dilewati'],
                'default' => 'dibersihkan',
            ],
            'revisi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('task_submission_id', 'r_task_submissions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('item_id', 'm_items', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('r_task_submission_items');

        // Create r_task_submission_actions table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'task_submission_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'item_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'dikerjakan' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('task_submission_id', 'r_task_submissions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('item_id', 'm_items', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('r_task_submission_actions');
    }

    public function down()
    {
        $this->forge->dropTable('r_task_submission_actions', true);
        $this->forge->dropTable('r_task_submission_items', true);
        $this->forge->dropTable('r_task_submissions', true);
        $this->forge->dropTable('r_shift_assignments', true);
        $this->forge->dropTable('r_shifts', true);
        $this->forge->dropTable('m_actions', true);
        $this->forge->dropTable('m_items', true);
        $this->forge->dropTable('m_locations', true);
        $this->forge->dropTable('m_users', true);
    }
}
