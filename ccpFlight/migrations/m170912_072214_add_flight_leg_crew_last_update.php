<?php
namespace common\modules\ccpFlight\migrations;

use yii\db\Migration;

class m170912_072214_add_flight_leg_crew_last_update extends Migration
{
    
    
    public function safeUp()
    {

        $this->addColumn('{{%ccp_flight_leg_crew}}', 'last_updated_at', $this->timestamp()->defaultValue(null));
        
    }

    public function safeDown()
    {
        $this->dropColumn('{{%ccp_flight_leg_crew}}', 'last_updated_at');
        return true;
    } 


}
