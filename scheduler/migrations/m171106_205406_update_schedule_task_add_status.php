<?php
namespace common\modules\scheduler\migrations;


use yii\db\Migration;

class m171106_205406_update_schedule_task_add_status extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%scheduler_task}}', 'status',$this->integer());
    }

    public function safeDown()
    {
        $this->dropColumn('{{%scheduler_task}}', 'status');

        return true;
    }


}
