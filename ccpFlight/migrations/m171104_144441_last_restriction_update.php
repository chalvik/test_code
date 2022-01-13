<?php

namespace common\modules\ccpFlight\migrations;

use common\modules\ccpFlight\models\FlightLeg;
use yii\db\Expression;
use yii\db\Migration;

class m171104_144441_last_restriction_update extends Migration
{
    
    public function safeUp()
    {
        $this->addColumn('{{%ccp_flight_leg}}', 'last_restriction_updated_at', $this->timestamp());
        FlightLeg::updateAll(['last_restriction_updated_at' => new Expression("CURRENT_TIMESTAMP - INTERVAL '3 hours'")]);
    }

    public function safeDown()
    {
        $this->dropColumn('{{%ccp_flight_leg}}', 'last_restriction_updated_at');
    }
    
}
