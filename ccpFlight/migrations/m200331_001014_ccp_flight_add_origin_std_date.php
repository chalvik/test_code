<?php
namespace common\modules\ccpFlight\migrations;

use yii\db\Migration;

/**
 * Class m200331_001014_ccp_flight_add_origin_std_date
 */
class m200331_001014_ccp_flight_add_origin_std_date extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%ccp_flight}}', 'origin_std_date', $this->timestamp()->defaultValue(null)->comment("оригинальное плановое время вылета "));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%ccp_flight}}', 'origin_std_date');
    }
}
