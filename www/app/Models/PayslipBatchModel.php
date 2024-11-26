<?php

namespace App\Models;

use CodeIgniter\Model;

class PayslipBatchModel extends Model
{
    protected $table = 'ci_payslip_batches';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'pay_date',
        'file',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'pay_date' => 'required|valid_date',
        'file'     => 'max_length[300]'
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
}