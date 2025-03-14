<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCalculatePayrollToCiErpUsersDetails extends Migration
{
	public function up()
	{
        $this->forge->addColumn('ci_erp_users_details', [
            'calculate_payroll' => [
                'type' => 'SMALLINT',
                'default' => false,
                'after' => 'occupancy'
            ]
        ]);
	}

	//--------------------------------------------------------------------

	public function down()
	{
		//
	}
}
