<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ActionSeeder extends Seeder
{
    public function run()
    {
        $db      = \Config\Database::connect();
        $builder = $db->table('m_actions');
        $builder->truncate();
        $db      = \Config\Database::connect();
        $inputFileName = '../cheklist_kebersihan.xlsx';
        $fileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($fileType);
        $reader->setReadDataOnly(false);
        $spreadsheet = $reader->load($inputFileName)->getSheetByName('all_actions');
        // Dapatkan jumlah baris tertinggi (last row)
        $highestRow = $spreadsheet->getHighestRow('B');

        $data = [];

        // Loop semua baris
        for ($row = 1; $row <= $highestRow; $row++) {
            $item = $spreadsheet->getCell('A' . $row)->getFormattedValue();
            $aksi = $spreadsheet->getCell('B' . $row)->getFormattedValue();
            array_push($data, [
                'nama_item' => $item,
                'nama_aksi' => $aksi
            ]);
        }

        $this->db->table('m_actions')->insertBatch($data);
    }
}
