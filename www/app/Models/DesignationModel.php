<?php
namespace App\Models;

use CodeIgniter\Model;

class DesignationModel extends Model {
 
    protected $table = 'ci_designations';

    protected $primaryKey = 'designation_id';
    
	// get all fields of table
    protected $allowedFields = [
        'designation_id',
        'department_id',
        'company_id',
        'designation_name',
        'description',
        'position_number',
        'frz',
        'reference',
        'funding',
        'account',
        'award',
        'class',
        'created_at'
    ];
	
	protected $validationRules = [];
	protected $validationMessages = [];
	protected $skipValidation = false;
	
}
?>