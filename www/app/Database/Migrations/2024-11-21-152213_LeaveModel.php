<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class LeaveModel extends Migration
{
	public function up()
	{
        $this->forge->addColumn('ci_leave_applications', [
            'job' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'department_id' => [
                'type' => 'BIGINT',
                'null' => true,
            ],
            'days' => [
                'type' => 'INT',
                'null' => true,
            ],
            'resumption_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'medical_certificate' => [
                'type' => 'SMALLINT',
                'default' => 0,
            ],
            'advance_pay' => [
                'type' => 'SMALLINT',
                'default' => 0,
            ],
            'supporting_document' => [
                'type' => 'SMALLINT',
                'default' => 0,
            ],
            'require_leave_fare' => [
                'type' => 'SMALLINT',
                'default' => 0,
            ],
            'to_be_spent_in' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'leave_address_mail' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'travel_departure_from' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'travel_departure_to' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'travel_departure_mode' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'travel_departure_company' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'travel_returning_from' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'travel_returning_to' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'travel_returning_mode' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'travel_returning_company' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_travelling_time_required' => [
                'type' => 'SMALLINT',
                'default' => 0,
            ],
            'travelling_time_remarks' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'declaration' => [
                'type' => 'TEXT',
                'null' => true,
            ]
        ]);

        $this->forge->modifyColumn('ci_leave_applications', [
            'reason' => [
                'name' => 'reason',
                'type' => 'TEXT',
                'null' => true,
            ]
        ]);
	}

	//--------------------------------------------------------------------

	public function down()
	{
		//
	}
}
