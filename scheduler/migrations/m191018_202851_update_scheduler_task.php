<?php
namespace common\modules\scheduler\migrations;

use yii\db\Migration;

/**
 * Class m180423_202851_update_scheduler_task
 */
class m191018_202851_update_scheduler_task extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%scheduler_task_log}}', 'extended_log', $this->text()->null());
        $this->addColumn('{{%scheduler_task}}', 'priority', $this->integer(2)->null());
        $this->addColumn('{{%scheduler_task}}', 'enable_log', $this->boolean()->null());
        $this->addColumn('{{%scheduler_task_run}}', 'priority', $this->integer(2)->null());
    }

    public function safeDown()
    {
        $this->dropColumn('{{%scheduler_task_log}}', 'extended_log');
        $this->dropColumn('{{%scheduler_task}}', 'priority');
        $this->dropColumn('{{%scheduler_task}}', 'enable_log');
        $this->dropColumn('{{%scheduler_task_run}}', 'priority');

        return true;
    }
}
