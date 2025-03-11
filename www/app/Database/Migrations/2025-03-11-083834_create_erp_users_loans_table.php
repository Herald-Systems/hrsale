<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateErpUsersLoansTable extends Migration
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
                'type'       => 'INT',
                'constraint' => '11',
                'unsigned' => true,
            ],
            'loan_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '300',
                'null'       => true,
            ],
            'institution' => [
                'type'       => 'VARCHAR',
                'constraint' => '300',
                'null'       => true,
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id', true);
//        $this->forge->addForeignKey('user_id', 'ci_erp_users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('ci_erp_users_loans');
    }

	//--------------------------------------------------------------------

	public function down()
	{
        $this->forge->dropTable('ci_erp_users_loans', true);
    }
}
