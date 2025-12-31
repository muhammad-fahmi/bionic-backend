<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskSubmissionActionModel extends Model
{
    protected $table = 'r_tasksubmission_actions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        "task_submission_id",
        "item_id",
        "nama",
        "dikerjakan",
        "revisi"
    ];
}
