<?php
namespace common\modules\ccpEmployee\migrations;

use yii\db\Migration;

class m170825_110517_update_ccp_employee_add_file extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%ccp_employee}}', 'file_id', $this->integer());
    }

    public function safeDown()
    {
        $this->dropColumn('{{%ccp_employee}}', 'file_id');
        return true;
    }

}
