<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToUsers extends Migration
{
	public function up()
	{
		$this->forge->addColumn('ci_erp_users', [
            'occupancy' => [
                'type'          => 'TEXT',
                'null'          => true,
                'after'         => 'country'
            ],
            'resident' => [
                'type'          => 'TINYINT',
                'default'       => 0,
                'after'         => 'occupancy'
            ],
            'dependant_declaration_logged' => [
                'type'          => 'TINYINT',
                'default'       => 0,
                'after'         => 'resident'
            ],
            'number_of_children' => [
                'type'          => 'INT',
                'default'       => 0,
                'after'         => 'dependant_declaration_logged'
            ]
        ]);
	}

	//--------------------------------------------------------------------

	public function down()
	{
		//
	}
}
