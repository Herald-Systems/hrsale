<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveCategoryFromDesignations extends Migration
{
	public function up()
	{
		$this->forge->dropColumn('ci_designations', 'category');
        $this->forge->dropColumn('ci_designations', 'step');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		//
	}
}
