<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProcessedPayslipsTable extends Migration
{
	public function up()
	{
		$this->forge->addColumn('ci_payslip_batches', [
            'processed' => [
                'type' => 'SMALLINT',
                'default' => 0,
                'after' => 'file',
            ],
            'processed_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'processed',
            ],
        ]);
	}

	//--------------------------------------------------------------------

	public function down()
	{
		//
	}
}
