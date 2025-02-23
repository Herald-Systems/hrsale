<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCLevelToCIDepartments extends Migration
{
	public function up()
	{
		$this->forge->addColumn('ci_departments', [
            'c_level' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                null => true,
            ]
        ]);
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropColumn('ci_departments', 'c_level');
	}
}
