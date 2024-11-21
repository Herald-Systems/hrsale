<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TimesheetModel extends Migration
{
    public function up()
    {
        $this->forge->addColumn('ci_timesheet', [
            'clock_in_location' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'clock_in_longitude'
            ],
            'clock_out_location' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'clock_out_longitude'
            ]
        ]);
    }

    //--------------------------------------------------------------------

    public function down()
    {
        $this->forge->dropColumn('ci_timesheet', ['latitude', 'longitude', 'location']);
    }
}
