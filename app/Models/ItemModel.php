<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemModel extends Model
{
    protected $table = 'm_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id',
        'lokasi_id',
        'nama',
        'nama_display',
        'type'
    ];

    public function getAllItem()
    {
        return $this->select('*')->findAll();
    }

    public function getAllItemWithLocation()
    {
        return $this->select('m_items.id as id, m_items.lokasi_id, m_items.nama, m_items.nama_display, m_items.type, m_locations.name')
                    ->join('m_locations', 'm_items.lokasi_id = m_locations.id', 'left')
                    ->findAll();
    }
}
