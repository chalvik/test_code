<?php

namespace common\modules\ccpFlight\migrations;

use yii\db\Migration;

class m171113_121450_update_ccpflight_add_blof_blon extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%ccp_flight}}', 'blof', $this->timestamp()->defaultValue(null)->comment("время снятия с тормоза"));
        $this->addColumn('{{%ccp_flight}}', 'blon', $this->timestamp()->defaultValue(null)->comment("время постановки на тормоз"));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%ccp_flight}}', 'blof');
        $this->dropColumn('{{%ccp_flight}}', 'blon');
        return true;
    }
}
