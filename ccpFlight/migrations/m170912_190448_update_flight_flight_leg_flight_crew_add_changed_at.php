<?php
namespace common\modules\ccpFlight\migrations;

use yii\db\Migration;

class m170912_190448_update_flight_flight_leg_flight_crew_add_changed_at extends Migration
{
    public function safeUp()
    {

        $this->addColumn('{{%ccp_flight}}', 'changed_at', $this->timestamp()->defaultValue(null));
        $this->addColumn('{{%ccp_flight_leg}}', 'changed_at', $this->timestamp()->defaultValue(null));
        $this->addColumn('{{%ccp_flight_leg_crew}}', 'changed_at', $this->timestamp()->defaultValue(null));
        
    }

    public function safeDown()
    {
        $this->dropColumn('{{%ccp_flight}}', 'changed_at');
        $this->dropColumn('{{%ccp_flight_leg}}', 'changed_at');
        $this->dropColumn('{{%ccp_flight_leg_crew}}', 'changed_at');
        return true;
    }}
