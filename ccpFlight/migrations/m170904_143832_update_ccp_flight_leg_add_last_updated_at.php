<?php
namespace common\modules\ccpFlight\migrations;

use yii\db\Migration;

class m170904_143832_update_ccp_flight_leg_add_last_updated_at extends Migration
{
    public function safeUp()
    {

        $this->addColumn('{{%ccp_flight_leg}}', 'last_updated_at', $this->timestamp()->defaultValue(null));
        
    }

    public function safeDown()
    {
        $this->dropColumn('{{%ccp_flight_leg}}', 'last_updated_at');
        return true;
    }   
}
