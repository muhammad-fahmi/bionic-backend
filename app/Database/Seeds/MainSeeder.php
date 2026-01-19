<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MainSeeder extends Seeder
{
    public function run()
    {
        echo "Starting database seeding...\n\n";

        // Seed users first
        $this->call('UserSeeder');

        $this->call('LocationSeeder');

        $this->call('ItemSeeder');

        $this->call('ActionSeeder');

        echo "\n✓ All seeders completed successfully!\n";
    }
}
