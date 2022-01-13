<?php
namespace common\modules\ccpFlight\migrations;

use yii\db\Migration;

/**
 * Class m171210_154047_update_ccp_flight_add_tkof_tdown
 */
class m171210_154047_update_ccp_flight_add_tkof_tdown extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%ccp_flight}}', 'tkof', $this->timestamp()->defaultValue(null)->comment("время отрыва от земли"));
        $this->addColumn('{{%ccp_flight}}', 'tdown', $this->timestamp()->defaultValue(null)->comment("время касания земли"));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%ccp_flight}}', 'tkof');
        $this->dropColumn('{{%ccp_flight}}', 'tdown');

        return true;
    }


}
