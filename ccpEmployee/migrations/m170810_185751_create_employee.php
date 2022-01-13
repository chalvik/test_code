<?php
namespace common\modules\ccpEmployee\migrations;


use yii\db\Migration;

class m170810_185751_create_employee extends Migration
{
    public function safeUp()
    {
        
        $this->createTable('{{%ccp_employee}}', [
            
            'id'            =>  $this->primaryKey(),
            'roster_id'     =>  $this->integer(),
            'name'          =>  $this->string()->comment(" имя в ростере "),
            'fio_eng'       =>  $this->string()->comment(" фио на английском1 "),
            'fio_rus'       =>  $this->string()->comment(" фио на русском "),
            'quals_list'    =>  $this->string()->comment(" квалификация - коды  "),
            'langs_list'    =>  $this->string()->comment(" языки "),
            'port_base'     =>  $this->string()->comment(" код аэропорта приписки  "),
            'block_hours'   =>  $this->string()->comment(" наработка часов "),
            'created_at'    =>  $this->timestamp()->defaultValue(null),
            'updated_at'    =>  $this->timestamp()->defaultValue(null),
            
        ]);           
        

    }

    
    public function safeDown()
    {
        $this->dropTable('ccp_employee');
        return true;
    }

}
