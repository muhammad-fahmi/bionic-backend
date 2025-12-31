<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ActivateCurrentShifts extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        echo "Setting up active shift assignments for operators...\n\n";

        // Deactivate all existing assignments
        $db->query('UPDATE r_shift_assignments SET is_active = 0');

        // Get all operators (petugas)
        $operators = $db->table('m_users')
            ->where('jabatan', 'petugas')
            ->get()
            ->getResultArray();

        if (empty($operators)) {
            echo "No operators found!\n";
            return;
        }

        $today = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime($today . ' +7 days'));

        // Assign shifts in round-robin fashion
        foreach ($operators as $index => $operator) {
            $shiftCode = ($index % 3) + 1;

            $db->table('r_shift_assignments')->insert([
                'user_id' => $operator['id'],
                'shift_code' => $shiftCode,
                'start_date' => $today,
                'end_date' => $endDate,
                'is_active' => 1,
                'created_by' => 1, // Admin
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            echo "✓ {$operator['nama']} (ID: {$operator['id']}) → Shift {$shiftCode} ({$today} to {$endDate})\n";
        }

        echo "\n✓ All operator shifts activated!\n";
    }
}
