<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run()
    {
        $db      = \Config\Database::connect();
        $builder = $db->table('m_items');
        $builder->truncate();
        $inputFileName = '../cheklist_kebersihan.xlsx';
        $fileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($fileType);
        $reader->setReadDataOnly(false);
        $shifts = ['Shift 1', 'Shift 2', 'Shift 3'];
        foreach ($shifts as $shift) {
            $spreadsheet = $reader->load($inputFileName)->getSheetByName($shift);
            // Dapatkan jumlah baris tertinggi (last row)
            $highestRow = $spreadsheet->getHighestDataRow('E');

            $data = [];

            // Loop semua baris
            for ($row = 2; $row <= $highestRow; $row++) {
                $nama = $spreadsheet->getCell('B' . $row)->getFormattedValue();
                $lokasi = $spreadsheet->getCell('E' . $row)->getOldCalculatedValue();
                array_push(
                    $data,
                    [
                        'nama' => $nama,
                        'lokasi_id' => $lokasi,
                    ]
                );
            }

            $this->db->table('m_items')->insertBatch($data);
        }
    }
}
