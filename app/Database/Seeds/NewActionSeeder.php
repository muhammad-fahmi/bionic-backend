<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NewActionSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Disable foreign key checks
        $db->query('SET FOREIGN_KEY_CHECKS=0');

        // Truncate the table
        $db->table('m_actions')->truncate();

        // Read data from Excel - activities sheet
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $excelPath = ROOTPATH . 'cheklist_kebersihan.xlsx';
        $spreadsheet = $reader->load($excelPath);
        $sheet = $spreadsheet->getSheetByName('activities');
        $data = $sheet->toArray();

        $actions = [];

        // Skip header row (index 0)
        for ($i = 1; $i < \count($data); $i++) {
            $row = $data[$i];
            $actionId = $row[0];
            $itemName = $row[1];
            $actionName = $row[2];
            $actionDisplay = $row[3];

            $actions[] = [
                'id' => $actionId,
                'nama_aksi' => $actionName,
                'nama_aksi_display' => $actionDisplay,
                'nama_item' => $itemName
            ];
        }

        // Insert actions
        if (!empty($actions)) {
            $db->table('m_actions')->insertBatch($actions);
            echo "Inserted " . \count($actions) . " actions\n";
        }

        // Re-enable foreign key checks
        $db->query('SET FOREIGN_KEY_CHECKS=1');
    }
}
