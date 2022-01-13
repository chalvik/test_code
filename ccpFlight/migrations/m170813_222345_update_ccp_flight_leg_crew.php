<?php
namespace common\modules\ccpFlight\migrations;

use yii\db\Migration;

class m170813_222345_update_ccp_flight_leg_crew extends Migration
{
    public function safeUp()
    {
        $this->renameColumn('ccp_flight_leg_crew', 'flight_leg_id', 'flight_id');
    }

    public function safeDown()
    {
        $this->renameColumn('ccp_flight_leg_crew', 'flight_id', 'flight_leg_id');
        return true;
    }

}
