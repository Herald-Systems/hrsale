<?php
namespace App\Models;

use CodeIgniter\Model;

class LeaveModel extends Model {
 
    protected $table = 'ci_leave_applications';

    protected $primaryKey = 'leave_id';
    
	// get all fields of table
    protected $allowedFields = [
        'leave_id','company_id','employee_id','leave_type_id','from_date','to_date','reason',
        'remarks','status','is_half_day','leave_attachment','created_at', 'department_id',
        'job','days','resumption_date','medical_certificate','advance_pay','supporting_document',
        'require_leave_fare','to_be_spent_in','leave_address_mail','travel_departure_from',
        'travel_departure_to','travel_departure_mode', 'travel_departure_company', 'travel_return_from', 'travel_return_to',
        'travel_return_mode','travel_return_company', 'is_travelling_time_required', 'travelling_time_remarks',
        'declaration'
    ];
	
	protected $validationRules = [];
	protected $validationMessages = [];
	protected $skipValidation = false;
	
}
?>