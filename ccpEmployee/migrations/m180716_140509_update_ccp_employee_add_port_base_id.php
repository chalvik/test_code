<?php

namespace common\modules\ccpEmployee\migrations;

use yii\db\Migration;

/**
 * Class m180716_140509_update_ccp_employee_add_port_base_id
 */
class m180716_140509_update_ccp_employee_add_port_base_id extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%ccp_employee}}', 'port_base_id', $this->integer());
    }

    public function safeDown()
    {
        $this->dropColumn('{{%ccp_employee}}', 'port_base_id');
        return true;
    }

}
