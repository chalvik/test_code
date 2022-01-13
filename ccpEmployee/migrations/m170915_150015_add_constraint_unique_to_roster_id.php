<?php

namespace common\modules\ccpEmployee\migrations;

use yii\db\Migration;

class m170915_150015_add_constraint_unique_to_roster_id extends Migration
{

    public function safeUp()
    {
        $this->createIndex('{{%ccp_employee_roster_id_unique}}', '{{%ccp_employee}}', 'roster_id', true);
    }

    public function safeDown()
    {
        $this->dropIndex('{{%ccp_employee_roster_id_unique}}', '{{%ccp_employee}}');
    }

}
