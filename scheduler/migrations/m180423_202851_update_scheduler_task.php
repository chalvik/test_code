<?php
namespace common\modules\scheduler\migrations;

use yii\db\Migration;

/**
 * Class m180423_202851_update_scheduler_task
 */
class m180423_202851_update_scheduler_task extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%scheduler_task}}', 'progress', $this->integer());
    }

    public function safeDown()
    {
        $this->dropColumn('{{%scheduler_task}}', 'progress');

        return true;
    }
}
