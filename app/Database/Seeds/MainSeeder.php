<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MainSeeder extends Seeder
{
    public function run()
    {
        $this->call("UserSeeder");
        $this->call("ItemSeeder");
        $this->call("LocationSeeder");
        $this->call("ActionSeeder");
    }
}
