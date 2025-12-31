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

        // Seed locations (must come before items due to foreign key)
        $this->call('NewLocationSeeder');

        // Seed items (must come after locations due to foreign key)
        $this->call('NewItemSeeder');

        // Seed actions
        $this->call('NewActionSeeder');

        // Setup shift assignments (depends on users)
        $this->call('ActivateCurrentShifts');

        echo "\n✓ All seeders completed successfully!\n";
    }
}
