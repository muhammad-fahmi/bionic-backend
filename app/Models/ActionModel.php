<?php

namespace App\Models;

use CodeIgniter\Model;

class ActionModel extends Model
{
    protected $table = 'm_actions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'nama_item',
        'nama_aksi',
    ];

    public function getAllAction()
    {
        return $this->select('*')->findAll();
    }
}
