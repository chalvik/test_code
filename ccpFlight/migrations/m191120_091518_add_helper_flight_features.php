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
class m191120_091518_add_helper_flight_features extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable('{{%helper_flight_features}}', [
            'flight_id' => $this->integer(),
            'codes' => 'TEXT []',
            'existed_codes' => 'TEXT []',
            'has_fail_transfers' => $this->boolean()
        ]);

        $this->addForeignKey('fk-helper_flight_features-flight_id-id',
            '{{%helper_flight_features}}',
            'flight_id',
            '{{%ccp_flight}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-helper_flight_features-flight_id-id',
            '{{%helper_flight_features}}');

        $this->dropTable('{{%helper_flight_features}');


    }

}
