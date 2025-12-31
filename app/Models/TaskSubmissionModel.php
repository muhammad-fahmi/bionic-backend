<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskSubmissionModel extends Model
{
    protected $table = 'r_tasksubmissions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        "id",
        "tanggal",
        "petugas",
        "shift",
        "lokasi_id",
        "status"
    ];
}
