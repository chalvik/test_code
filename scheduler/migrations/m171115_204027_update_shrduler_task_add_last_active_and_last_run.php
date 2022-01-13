<?php

namespace common\modules\scheduler\migrations;


use yii\db\Migration;

class m171115_204027_update_shrduler_task_add_last_active_and_last_run extends Migration
{
    public function safeUp()
    {
        
        $this->addColumn('{{%scheduler_task}}', 'last_activity_at', $this->timestamp()->defaultValue(null)->comment(" время последней активности "));
        $this->addColumn('{{%scheduler_task}}', 'last_run_at', $this->timestamp()->defaultValue(null)->comment(" время последнего запуска "));
        $this->addColumn('{{%scheduler_task}}', 'last_addline_at', $this->timestamp()->defaultValue(null)->comment(" время последнего запуска "));
        
    }

    public function safeDown()
    {
        
        $this->dropColumn('{{%scheduler_task}}', 'last_activity_at');
        $this->dropColumn('{{%scheduler_task}}', 'last_run_at');
        $this->dropColumn('{{%scheduler_task}}', 'last_addline_at');

        return true;
    }

}
