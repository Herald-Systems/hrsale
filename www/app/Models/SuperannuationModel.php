<?php

namespace App\Models;

use CodeIgniter\Model;

class SuperannuationModel extends Model
{
    protected $table = 'ci_erp_users_superannuation';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id', 'user_id', 'nambawan_super', 'posf_super', 'pos_voluntary_amount'
    ];
}