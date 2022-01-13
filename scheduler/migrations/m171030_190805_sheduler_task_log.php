<?php
namespace common\modules\scheduler\migrations;


use yii\db\Migration;

class m171030_190805_sheduler_task_log extends Migration
{
    
    public function safeUp()
    {
        $this->createTable('{{%scheduler_task_log}}', [
            
            'id'           => $this->primaryKey(),
            'task_run_id'  => $this->integer()->comment(" процесса запуска задачи "),
            'status'       => $this->integer()->comment(" статус "),
            'message'      => $this->text()->comment(" сообщение ошибки или обновления "),
            'created_at'   => $this->timestamp()->defaultValue(null)->comment(" время дата создания "),
            
        ]);
        
//         $this->addForeignKey('{{%scheduler_task_run_log_fk}}', '{{%scheduler_task_log}}', 'task_run_id', '{{%scheduler_task_run}}', 'id', 'CASCADE', 'CASCADE');
    }
    
    
    public function safeDown()
    {
        $this->dropTable('{{%scheduler_task_log}}');
        return true;
    }

}
