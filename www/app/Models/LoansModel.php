<?php

namespace App\Models;

use CodeIgniter\Model;

class LoansModel extends Model
{
    protected $table = 'ci_erp_users_loans';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'id', 'user_id', 'loan_id', 'institution', 'amount'
    ];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}