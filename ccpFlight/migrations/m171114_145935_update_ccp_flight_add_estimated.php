<?php
namespace common\modules\ccpFlight\migrations;

use yii\db\Migration;

class m171114_145935_update_ccp_flight_add_estimated extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%ccp_flight}}', 'estimated', $this->timestamp()->defaultValue(null)->comment("расчетное время начала рейса "));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%ccp_flight}}', 'estimated');
    }

}
