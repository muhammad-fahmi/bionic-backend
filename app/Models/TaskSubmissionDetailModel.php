<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskSubmissionDetailModel extends Model
{
    protected $table = 'r_task_submission_detail';
    protected $primaryKey = 'task_submission_detail_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'task_submission_id',
        'action_id',
        'quantity'
    ];
}
