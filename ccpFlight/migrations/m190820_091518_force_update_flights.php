<?php

namespace common\modules\ccpFlight\migrations;

use yii\db\Migration;

/**
 * Class m190820_091518_flight_brief
 */
class m190820_091518_force_update_flights extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable('{{%ccp_flight_force_update}}', [
            'flight_id' => $this->primaryKey(),
            'created_at' => $this->timestamp()->defaultValue(null),
            'updated_at' => $this->timestamp()->defaultValue(null),
            'is_updated' => $this->boolean()->defaultValue(false)
        ]);


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropTable('{{%ccp_flight_force_update}}');

    }

}
