<?php

namespace common\modules\ccpFlight\migrations;

use common\modules\cargo\models\FlightLoadCompartment;
use common\modules\cargo\models\FlightLoadDetail;
use common\modules\cargo\models\FlightMail;
use common\modules\ccpFlight\models\FeatureFlight;
use common\modules\ccpFlight\models\Flight;
use yii\db\Migration;

/**
 * Class m190820_091518_flight_brief
 */
class m191202_120000_alter_flight_features_add_column_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%feature_flight}}',
            'type', $this->smallInteger(1)->null());
        // make all old types as external
        FeatureFlight::updateAll(['type' => FeatureFlight::TYPE_EXTERNAL]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%feature_flight}}', 'type');
    }

}
