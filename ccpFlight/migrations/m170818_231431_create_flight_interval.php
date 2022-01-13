<?php

namespace common\modules\ccpFlight\migrations;

use yii\db\Migration;

class m170818_231431_create_flight_interval extends Migration
{
    public function safeUp()
    {
        
        $this->createTable('ccp_flight_interval', [
            
            'id'                =>  $this->primaryKey(),
            'title'             =>  $this->string()->notNull()->comment('Название интервала'),
            'min'               =>  $this->float(3,1)->notNull()->comment('Минимаьное значение для авто установки'),
            'max'               =>  $this->float(3,1)->notNull()->comment('Максимальное значение для авто установки'),
            'created_at'        =>  $this->timestamp()->defaultValue(null),
            'updated_at'        =>  $this->timestamp()->defaultValue(null),
            
        ]);

    }

    
    public function safeDown()
    {
        $this->dropTable('ccp_flight_interval');
        
        return true;
    }

   
}
