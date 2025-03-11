<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateErpUsersSuperannuationTable extends Migration
{
	public function up()
	{
        $this->forge->addField( [
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'          => 'INT',
                'constraint'    => '11',
                'unsigned'      => true,
            ],
            'nambawan_super' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'   => 8.4,
            ],
            'posf_super' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'   => '6.0',
            ],
            'pos_voluntary_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'      => true
            ],
        ]);
        $this->forge->addKey('id', true);
//        $this->forge->addForeignKey('user_id', 'ci_erp_users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('ci_erp_users_superannuation');
	}

	//--------------------------------------------------------------------

	public function down()
	{
        $this->forge->dropTable('ci_erp_users_superannuation', true);
    }
}
