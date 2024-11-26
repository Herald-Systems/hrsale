<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePayslipsTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'pay_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'file' => [
                'type'       => 'VARCHAR',
                'constraint' => '300',
                'null'       => true,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('ci_payslip_batches');
	}

	//--------------------------------------------------------------------

	public function down()
	{
        $this->forge->dropTable('ci_payslip_batches');

    }
}
