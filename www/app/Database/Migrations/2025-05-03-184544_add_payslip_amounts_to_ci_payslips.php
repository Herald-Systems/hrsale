<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPayslipAmountsToCiPayslips extends Migration
{
	public function up()
	{
        $this->forge->addColumn('ci_payslips', [
            'annual_salary' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.0,
                'after' => 'basic_salary'
            ],
            'gross_salary' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.0,
                'after' => 'total_allowances'
            ],
            'super_employer_contribution' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.0,
                'after' => 'total_commissions'
            ],
            'total_super_employee' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.0,
                'after' => 'total_commissions'
            ],
            'super_employee_voluntary' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.0,
                'after' => 'total_commissions'
            ],
            'super_employee_compulsory' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.0,
                'after' => 'total_commissions'
            ],
            'net_tax' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.0,
                'after' => 'total_commissions'
            ],
            'dependent_rebate' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.0,
                'after' => 'total_commissions'
            ],
            'gross_tax' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.0,
                'after' => 'total_commissions'
            ],
            'total_deductions' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.0,
                'after' => 'total_other_payments'
            ],
        ]);
	}

	//--------------------------------------------------------------------

	public function down()
	{
		//
	}
}
