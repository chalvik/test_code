<?php
namespace common\modules\scheduler\migrations;


use yii\db\Migration;

class m171030_190811_sheduler_task_run extends Migration
{
    
    public function safeUp()
    {
        $this->createTable('{{%scheduler_task_run}}', [
            
            'id'                => $this->primaryKey(),
            'task_id'           => $this->integer()->comment(" задача "),
            'started_at'        => $this->timestamp()->defaultValue(null)->comment(" время дата начала выполнения "),
            'finished_at'       => $this->timestamp()->defaultValue(null)->comment(" время дата окончания выполнения "),
            'status'            => $this->integer()->comment(" статус выполнения задачи "),
            
        ]);
        
         $this->addForeignKey('{{%scheduler_task_run_fk}}', '{{%scheduler_task_run}}', 'task_id', '{{%scheduler_task}}', 'id', 'CASCADE', 'CASCADE');
                 
    }

    public function safeDown()
    {
        $this->dropTable('{{%scheduler_task_run}}');
        return true;
    }


}
