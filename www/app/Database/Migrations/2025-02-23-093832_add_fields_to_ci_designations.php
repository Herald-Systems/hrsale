<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToCiDesignations extends Migration
{
	public function up()
	{
		$this->forge->addColumn('ci_designations', [
            'position_number' => [
                'type'              => 'VARCHAR',
                'constraint'        => 255,
                'null'              => true,
                'after'             => 'description',
            ],
            'frz' => [
                'type'              => 'SMALLINT',
                'null'              => true,
                'after'             => 'position_number',
            ],
            'reference' => [
                'type'              => 'VARCHAR',
                'constraint'        => 255,
                'null'              => true,
                'after'             => 'frz',
            ],
            'funding' => [
                'type'              => 'VARCHAR',
                'constraint'        => 255,
                'null'              => true,
                'after'             => 'reference',
            ],
            'account' => [
                'type'              => 'VARCHAR',
                'constraint'        => 255,
                'null'              => true,
                'default'           => '584-02-201-111',
                'after'             => 'funding',
            ],
            'award' => [
                'type'              => 'VARCHAR',
                'constraint'        => 255,
                'null'              => true,
                'after'             => 'account',
            ],
            'category' => [
                'type'              => 'VARCHAR',
                'constraint'        => 255,
                'null'              => true,
                'after'             => 'award',
            ],
            'class' => [
                'type'              => 'VARCHAR',
                'constraint'        => 255,
                'null'              => true,
                'after'             => 'category',
            ],
            'step' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'null'              => true,
                'after'             => 'class',
            ]
        ]);
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropColumn(
            'ci_designations',
            [
                'position_number',
                'frz', 'reference',
                'funding',
                'account',
                'award',
                'category',
                'class',
                'step'
            ]
        );
	}
}
