<?php
namespace common\modules\ccpFlight\migrations;

use yii\db\Migration;

/**
 * Class m180723_140349_update_flight_leg_for_legflight
 */
class m180723_140349_update_flight_leg_for_legflight extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%ccp_flight_leg}}', 'fltDes', $this->string());
        $this->addColumn('{{%ccp_flight_leg}}', 'os', $this->string(2));
        $this->addColumn('{{%ccp_flight_leg}}', 'interval', $this->integer());
        $this->addColumn('{{%ccp_flight_leg}}', 'menu_id', $this->integer());
        $this->addColumn('{{%ccp_flight_leg}}', 'deleted', $this->integer());
        $this->addColumn('{{%ccp_flight_leg}}', 'deleted_at', $this->timestamp()->defaultValue(null));
        $this->addColumn('{{%ccp_flight_leg}}', 'deleted_user_id', $this->integer());
        $this->addColumn('{{%ccp_flight_leg}}', 'updated_user_id', $this->integer());
        $this->addColumn('{{%ccp_flight_leg}}', 'estimated', $this->timestamp()->defaultValue(null));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%ccp_flight_leg}}', 'os');
        $this->dropColumn('{{%ccp_flight_leg}}', 'fltDes');
        $this->dropColumn('{{%ccp_flight_leg}}', 'interval');
        $this->dropColumn('{{%ccp_flight_leg}}', 'menu_id');
        $this->dropColumn('{{%ccp_flight_leg}}', 'deleted');
        $this->dropColumn('{{%ccp_flight_leg}}', 'deleted_at');
        $this->dropColumn('{{%ccp_flight_leg}}', 'deleted_user_id');
        $this->dropColumn('{{%ccp_flight_leg}}', 'updated_user_id');
        $this->dropColumn('{{%ccp_flight_leg}}', 'estimated');
        return true;
    }
}
