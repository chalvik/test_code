<?php
namespace common\modules\ccpFlight\migrations;

use yii\db\Migration;

/**
 * Handles adding pos_pu to table `ccp_flight_leg_crew`.
 */
class m190114_090543_add_pos_pu_column_to_ccp_flight_leg_crew_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('ccp_flight_leg_crew', 'pos_pu',
            $this->string()->defaultValue(null)->comment("новое технологическое место")
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('ccp_flight_leg_crew', 'pos_pu');
    }
}
