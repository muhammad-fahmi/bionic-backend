<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $db      = \Config\Database::connect();
        $builder = $db->table('m_users');
        $builder->truncate();
        $data = [
            [
                'nama' => 'Yanto',
                'jabatan'    => 'petugas',
                'username' => 'yanto',
                'password' => password_hash("yanto", PASSWORD_BCRYPT)
            ],
            [
                'nama' => 'Ari',
                'jabatan'    => 'petugas',
                'username' => 'ari',
                'password' => password_hash("ari", PASSWORD_BCRYPT)
            ],
            [
                'nama' => 'Budi',
                'jabatan'    => 'petugas',
                'username' => 'budi',
                'password' => password_hash("budi", PASSWORD_BCRYPT)
            ],
            [
                'nama' => 'Rian',
                'jabatan'    => 'verifikator',
                'username' => 'rian',
                'password' => password_hash("rian", PASSWORD_BCRYPT)
            ],
            [
                'nama' => 'Admin',
                'jabatan'    => 'admin',
                'username' => 'admin',
                'password' => password_hash("admin", PASSWORD_BCRYPT)
            ],
        ];

        $this->db->table('m_users')->insertBatch($data);
    }
}
