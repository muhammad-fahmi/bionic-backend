<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NewLocationSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Disable foreign key checks
        $db->query('SET FOREIGN_KEY_CHECKS=0');

        // Truncate the table
        $db->table('m_locations')->truncate();

        // Read data from Excel
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $excelPath = ROOTPATH . 'cheklist_kebersihan.xlsx';
        $spreadsheet = $reader->load($excelPath);
        $sheet = $spreadsheet->getSheetByName('tasks');
        $data = $sheet->toArray();

        // Track unique locations
        $locations = [];
        $locationMap = []; // Map to track location ID -> name

        // Skip header row (index 0)
        for ($i = 1; $i < count($data); $i++) {
            $row = $data[$i];
            $locationId = $row[0];
            $shift = $row[1];
            $locationName = $row[2];

            // Only add unique locations
            if (!isset($locationMap[$locationId])) {
                $locationMap[$locationId] = $locationName;
                $locations[] = [
                    'id' => $locationId,
                    'name' => $locationName,
                    'shift' => $shift
                ];
            }
        }

        // Insert locations
        if (!empty($locations)) {
            $db->table('m_locations')->insertBatch($locations);
            echo "Inserted " . count($locations) . " locations\n";
        }

        // Re-enable foreign key checks
        $db->query('SET FOREIGN_KEY_CHECKS=1');
    }
}
