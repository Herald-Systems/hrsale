<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCategoryToUsers extends Migration
{
	public function up()
	{
		$this->forge->addColumn('ci_erp_users_details', [
            'category' => [
                'type'              => 'VARCHAR',
                'constraint'        => 255,
                'null'              => true,
                'after'             => 'contact_address',
            ],
            'step' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'null'              => true,
                'after'             => 'category',
            ]
        ]);
	}

	//--------------------------------------------------------------------

	public function down()
	{
		//
	}
}
