<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CoreTable extends Migration
{
    public function up()
    {
        /* ------------------------------- MASTER DATA ------------------------------ */
        // Master Users
        $this->forge->addField([
            "id" => [
                "type" => "INT",
                "contraint" => 5,
                "auto_increment" => true
            ],
            "nama" => [
                "type" => "VARCHAR",
                "constraint" => 200,
                "unique" => true
            ],
            "jabatan"  => [
                "type" => "ENUM",
                'constraint' => ['petugas', 'verifikator', 'admin'],
                'default'    => 'petugas',
            ],
            "username" => [
                "type" => "VARCHAR",
                "constraint" => 150,
                "unique" => true
            ],
            "password" => [
                "type" => "VARCHAR",
                "constraint" => 255
            ]
        ]);
        $this->forge->addKey("id", true);
        $this->forge->createTable("m_users");

        // Master Locations
        $this->forge->addField([
            "id" => [
                "type" => "INT",
                "contraint" => 5,
                "auto_increment" => true
            ],
            "lokasi" => [
                "type" => "VARCHAR",
                "constraint" => 100,
            ],
            "shift" => [
                "type" => "INT",
                "constraint" => 5
            ]
        ]);
        $this->forge->addKey("id", true);
        $this->forge->createTable("m_locations");

        // Master Items
        $this->forge->addField([
            "id" => [
                "type" => "INT",
                "contraint" => 5,
                "auto_increment" => true
            ],
            "lokasi_id" => [
                "type" => "INT",
                "constraint" => 3,
            ],
            "nama" => [
                "type" => "VARCHAR",
                "constraint" => 150,
            ]

        ]);
        $this->forge->addKey("id", true);
        $this->forge->createTable("m_items");

        // Master Actions
        $this->forge->addField([
            "id" => [
                "type" => "INT",
                "contraint" => 5,
                "auto_increment" => true
            ],
            "nama_item" => [
                "type" => "VARCHAR",
                "constraint" => 50,
            ],
            "nama_aksi" => [
                "type" => "VARCHAR",
                "constraint" => 200,
            ]

        ]);
        $this->forge->addKey("id", true);
        $this->forge->createTable("m_actions");


        /* ----------------------------- END MASTER DATA ---------------------------- */

        // Task Submission History
        $this->forge->addField([
            "id" => [
                "type" => "INT",
                "contraint" => 5,
                "auto_increment" => true
            ],
            "tanggal" => [
                "type" => "DATETIME"
            ],
            "petugas" => [
                "type" => "VARCHAR",
                "constraint" => 50,
            ],
            "shift" => [
                "type" => "INT",
                "constraint" => 5
            ],
            "lokasi_id" => [
                "type" => "INT",
                "constraint" => 5
            ],
            "status" => [
                "type" => "ENUM",
                "constraint" => ['pending', 'approved', 'rejected'],
                'default' => 'pending'
            ]
        ]);
        $this->forge->addKey("id", true);
        $this->forge->createTable("r_tasksubmissions");

        /* ------------------------- TASK SUBMISSION DETAIL ------------------------- */
        // Task Submission Items
        $this->forge->addField([
            "id" => [
                "type" => "INT",
                "contraint" => 5,
                "auto_increment" => true
            ],
            "task_submission_id" => [
                "type" => "INT",
                "contraint" => 5,
            ],
            "item_id" => [
                "type" => "INT",
                "constraint" => 3
            ],
            "kondisi" => [
                "type" => "ENUM",
                "constraint" => ['bersih', 'kotor'],
                "default" => 'kotor'
            ]
        ]);
        $this->forge->addKey("id", true);
        $this->forge->createTable("r_tasksubmission_items");

        // Task Submission Attachment
        $this->forge->addField([
            "id" => [
                "type" => "INT",
                "contraint" => 5,
                "auto_increment" => true
            ],
            "task_submission_id" => [
                "type" => "INT",
                "contraint" => 5,
            ],
            "upload_path" => [
                "type" => "VARCHAR",
                "constraint" => 200
            ],
            "uploaded_at" => [
                "type" => "DATETIME"
            ]
        ]);
        $this->forge->addKey("id", true);
        $this->forge->createTable("r_tasksubmission_attachs");

        // Task Submission Actions
        $this->forge->addField([
            "id" => [
                "type" => "INT",
                "contraint" => 5,
                "auto_increment" => true
            ],
            "task_submission_id" => [
                "type" => "INT",
                "contraint" => 5,
            ],
            "item_id" => [
                "type" => "INT",
                "contraint" => 5,
            ],
            "nama" => [
                "type" => "VARCHAR",
                "constraint" => 100
            ],
            "deskripsi" => [
                "type" => "VARCHAR",
                "constraint" => 150,
                "default" => ""
            ]
        ]);
        $this->forge->addKey("id", true);
        $this->forge->createTable("r_tasksubmission_actions");
        /* ----------------------- END TASK SUBMISSION DETAIL ----------------------- */

        /* ------------------------------ SHIFT HISTORY ----------------------------- */
        $this->forge->addField([
            "id" => [
                "type" => "INT",
                "contraint" => 5,
                "auto_increment" => true
            ],
            "user_id" => [
                "type" => "INT",
                "contraint" => 5,
            ],
            "shift_code" => [
                "type" => "INT",
                "contraint" => 5,
            ],
            "shift_date" => [
                "type" => "DATE",
            ],
            "created_at" => [
                "type" => "DATETIME",
            ],
            "updated_at" => [
                "type" => "DATETIME",
            ],
            "deleted_at" => [
                "type" => "DATETIME",
            ]
        ]);
        $this->forge->addKey("id", true);
        $this->forge->createTable("r_shifts");
        /* ---------------------------- END SHIFT HISTORY --------------------------- */
    }

    public function down()
    {
        $this->forge->dropTable('m_users', true);
        $this->forge->dropTable('m_locations', true);
        $this->forge->dropTable('m_items', true);
        $this->forge->dropTable('m_tasks', true);
        $this->forge->dropTable('m_actions', true);
        $this->forge->dropTable('r_tasksubmissions', true);
        $this->forge->dropTable('r_tasksubmission_items', true);
        $this->forge->dropTable('r_tasksubmission_attachs', true);
        $this->forge->dropTable('r_tasksubmission_actions', true);
        $this->forge->dropTable('r_shifts', true);
    }
}
