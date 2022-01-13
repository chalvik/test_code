<?php

namespace common\modules\scheduler\migrations;

use yii\db\Migration;

/**
 * Class m180123_193118_update_scheduler_log_add_task_id
 */
class m180123_193118_update_scheduler_log_add_task_id extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%scheduler_task_log}}', 'task_id', $this->integer());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%scheduler_task_log}}', 'task_id');
        return true;
    }

}
