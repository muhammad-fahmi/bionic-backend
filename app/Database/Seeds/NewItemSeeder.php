<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NewItemSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Disable foreign key checks
        $db->query('SET FOREIGN_KEY_CHECKS=0');

        // Truncate the table
        $db->table('m_items')->truncate();

        // Read data from Excel - tasks sheet
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $excelPath = ROOTPATH . 'cheklist_kebersihan.xlsx';
        $spreadsheet = $reader->load($excelPath);

        $tasksSheet = $spreadsheet->getSheetByName('tasks');
        $completeTaskData = $tasksSheet->toArray();

        $items = [];
        $itemId = 1;

        // Process complete_task items (skip header row)
        for ($i = 1; $i < \count($completeTaskData); $i++) {
            $row = $completeTaskData[$i];
            $locationId = $row[0];
            $itemName = $row[3];
            $itemDisplay = $row[4];

            $items[] = [
                'id' => $itemId++,
                'nama' => $itemName,
                'nama_display' => $itemDisplay,
                'type' => 'complete_task',
                'lokasi_id' => $locationId
            ];
        }

        // Read data from Excel - additional_task sheet
        $additionalTaskSheet = $spreadsheet->getSheetByName('additional_task');
        $additionalTaskData = $additionalTaskSheet->toArray();

        // Process additional_task items (skip header row)
        // Additional tasks are not location-specific, assign to location 1 as default
        for ($i = 1; $i < \count($additionalTaskData); $i++) {
            $row = $additionalTaskData[$i];
            $itemName = $row[1];
            $itemDisplay = $row[2];

            $items[] = [
                'id' => $itemId++,
                'nama' => $itemName,
                'nama_display' => $itemDisplay,
                'type' => 'additional_task',
                'lokasi_id' => 1 // Default location for additional tasks
            ];
        }

        // Insert items
        if (!empty($items)) {
            $db->table('m_items')->insertBatch($items);
            echo "Inserted " . \count($items) . " items\n";
            echo "  - Complete tasks: " . \count($completeTaskData) - 1 . "\n";
            echo "  - Additional tasks: " . \count($additionalTaskData) - 1 . "\n";
        }

        // Re-enable foreign key checks
        $db->query('SET FOREIGN_KEY_CHECKS=1');
    }
}
