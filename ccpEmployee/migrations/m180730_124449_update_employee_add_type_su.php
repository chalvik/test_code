<?php

namespace common\modules\ccpEmployee\migrations;

use yii\db\Migration;

/**
 * Class m180730_124449_update_employee_add_type_su
 */
class m180730_124449_update_employee_add_type_su extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%ccp_employee}}', 'type', $this->integer()->defaultValue(0));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%ccp_employee}}', 'type');
        return true;
    }
}
