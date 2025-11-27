<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run()
    {
        $db      = \Config\Database::connect();
        $builder = $db->table('m_locations');
        $builder->truncate();
        $inputFileName = '../cheklist_kebersihan.xlsx';
        $fileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($fileType);
        $reader->setReadDataOnly(false);
        $spreadsheet = $reader->load($inputFileName)->getSheetByName('all_locations');
        // Dapatkan jumlah baris tertinggi (last row)
        $highestRow = $spreadsheet->getHighestRow('D');

        $data = [];

        // Loop semua baris
        for ($row = 1; $row <= $highestRow; $row++) {
            $shift_idx = $spreadsheet->getCell('D' . $row)->getFormattedValue();
            $result = $spreadsheet->getCell('E' . $row)->getFormattedValue();
            array_push($data, [
                'lokasi' => $result,
                'shift' => $shift_idx
            ]);
        }

        $this->db->table('m_locations')->insertBatch($data);
    }
}
