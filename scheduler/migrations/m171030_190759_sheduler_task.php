<?php

namespace common\modules\scheduler\migrations;

use yii\db\Migration;

class m171030_190759_sheduler_task extends Migration
{
    
    public function safeUp()
    {
        $this->createTable('{{%scheduler_task}}', [
            
            'id'                => $this->primaryKey(),
            'title'             => $this->string()->comment("Название задачи"),
            'class'             => $this->string()->comment("Класс для выполнения задания"),
            'period'            => $this->integer()->comment("Период обновления "),
            'user_created_id'   => $this->integer()->comment("Пользователь который создал задачу"),
            'user_updated_id'   => $this->integer()->comment("Пользователь который обновил задачу"),
            'created_at'        => $this->timestamp()->defaultValue(null)->comment(" время дата создания "),
            'updated_at'        => $this->timestamp()->defaultValue(null)->comment(" время дата обновления "),
            
        ]);
                
    }

    public function safeDown()
    {
        $this->dropTable('{{%scheduler_task}}');
        return true;
    }

}
