<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOccupancyToCiErpUsersDetails extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('ci_erp_users', 'occupancy');

        $this->forge->addColumn('ci_erp_users_details', [
            'occupancy' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'contact_address'
            ]
        ]);
    }

    //--------------------------------------------------------------------

    public function down()
    {
        //
    }
}
