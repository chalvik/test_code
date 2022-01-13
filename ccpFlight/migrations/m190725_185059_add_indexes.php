<?php
namespace common\modules\ccpFlight\migrations;

use yii\db\Migration;

class m190725_185059_add_indexes extends Migration
{
    public function safeUp()
    {

        $this->createIndex('idx-ccp_flight-flt', '{{%ccp_flight}}','flt');
        $this->createIndex('idx-ccp_flight-dep_airport_id', '{{%ccp_flight}}','dep_airport_id');
        $this->createIndex('idx-ccp_flight-arr_airport_id', '{{%ccp_flight}}','arr_airport_id');


        $this->createIndex('idx-ccp_flight_leg-flight_id','{{%ccp_flight_leg}}','flight_id');
        $this->createIndex('idx-ccp_flight_leg_crew-flight_id','{{%ccp_flight_leg_crew}}','flight_id');
        $this->createIndex('idx-ccp_notification-flight_id', '{{%ccp_notification}}','flight_id');
        $this->createIndex('idx-ccp_user_flight-flight_id', '{{%ccp_user_flight}}','flight_id');
        $this->createIndex('idx-ccp_user_token-user_id', '{{%ccp_user_token}}','user_id');
        $this->createIndex('idx-ccp_user_carrier-carrier', '{{%ccp_user_carrier}}','carrier');
        $this->createIndex('idx-ccp_user-roster_id', '{{%ccp_user}}','roster_id');



    }

    public function safeDown()
    {
        $this->dropIndex('idx-ccp_flight_leg-flight_id','{{%ccp_flight_leg}}');
        $this->dropIndex('idx-ccp_flight_leg_crew-flight_id','{{%ccp_flight_leg_crew}}');
        $this->dropIndex('idx-ccp_notification-flight_id', '{{%ccp_notification}}');
        $this->dropIndex('idx-ccp_user_flight-flight_id', '{{%ccp_user_flight}}');
        $this->dropIndex('idx-ccp_user_token-user_id', '{{%ccp_user_token}}');
        $this->dropIndex('idx-ccp_user_carrier-user_id', '{{%ccp_user_carrier}}');
        $this->dropIndex('idx-ccp_user-roster_id', '{{%ccp_user}}');


        $this->dropIndex('idx-ccp_flight-flt', '{{%ccp_flight}}','flt');
        $this->dropIndex('idx-ccp_flight-dep_airport_id', '{{%ccp_flight}}');
        $this->dropIndex('idx-ccp_flight-arr_airport_id', '{{%ccp_flight}}');

        return true;
    }


}
