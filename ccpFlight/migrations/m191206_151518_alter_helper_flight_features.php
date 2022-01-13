<?php

namespace common\modules\ccpFlight\migrations;

use common\modules\cargo\models\FlightLoadCompartment;
use common\modules\cargo\models\FlightLoadDetail;
use common\modules\cargo\models\FlightMail;
use common\modules\ccpFlight\models\Flight;
use yii\db\Migration;

/**
 * Class m190820_091518_flight_brief
 */
class m191206_151518_alter_helper_flight_features extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%helper_flight_features}}',
            'crew_pos', 'TEXT []');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%helper_flight_features}}','crew_pos');
    }

}
