<?php
namespace common\modules\ccpFlight\migrations;

use yii\db\Migration;

class m170815_183246_update_ccp_flight_and_flight_leg extends Migration
{
    public function safeUp()
    {
        
        $this->addColumn('ccp_flight', 'dep_stand', $this->string());
        $this->addColumn('ccp_flight', 'arr_stand', $this->string());
        $this->addColumn('ccp_flight', 'interval', $this->integer());

        $this->addColumn('ccp_flight_leg', 'dep_stand', $this->string());
        $this->addColumn('ccp_flight_leg', 'arr_stand', $this->string());

        
    }

    public function safeDown()
    {
        
       $this->dropColumn('ccp_flight', 'dep_stand');
       $this->dropColumn('ccp_flight', 'arr_stand');
       $this->dropColumn('ccp_flight', 'interval');
       $this->dropColumn('ccp_flight_leg', 'dep_stand');
       $this->dropColumn('ccp_flight_leg', 'arr_stand');
       
       return true;
        
    }

}
