<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Disable foreign key checks
        $db->query('SET FOREIGN_KEY_CHECKS=0');

        $builder = $db->table('m_users');
        $builder->truncate();

        $data = [
            [
                'nama' => 'Admin',
                'jabatan' => 'admin',
                'username' => 'admin',
                'password' => password_hash("admin", PASSWORD_BCRYPT)
            ],
            [
                'nama' => 'Yanto',
                'jabatan' => 'petugas',
                'username' => 'yanto',
                'password' => password_hash("yanto", PASSWORD_BCRYPT)
            ],
            [
                'nama' => 'Theo',
                'jabatan' => 'petugas',
                'username' => 'theo',
                'password' => password_hash("theo", PASSWORD_BCRYPT)
            ],
            [
                'nama' => 'Octo',
                'jabatan' => 'petugas',
                'username' => 'octo',
                'password' => password_hash("octo", PASSWORD_BCRYPT)
            ],
            [
                'nama' => 'Rian',
                'jabatan' => 'verifikator',
                'username' => 'rian',
                'password' => password_hash("rian", PASSWORD_BCRYPT)
            ],
        ];

        $db->table('m_users')->insertBatch($data);
        echo "Inserted " . \count($data) . " users\n";

        // Re-enable foreign key checks
        $db->query('SET FOREIGN_KEY_CHECKS=1');
    }
}
