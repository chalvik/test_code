<?php
namespace common\modules\ccpEmployee\migrations;

use yii\db\Migration;

class m170904_143909_update_ccp_employee_add_last_updated_at extends Migration
{
    public function safeUp()
    {

        $this->addColumn('{{%ccp_employee}}', 'last_updated_at', $this->timestamp()->defaultValue(null));
        
    }

    public function safeDown()
    {
        $this->dropColumn('{{%ccp_employee}}', 'last_updated_at');
        return true;
    }    
}
