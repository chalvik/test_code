<?php
namespace common\modules\ccpFlight\migrations;

use yii\db\Migration;

class m170808_185119_create_cpp_flight_leg_crew extends Migration
{
    
    public function safeUp()
    {
        
        $this->createTable('ccp_flight_leg_crew', [
            
            'id'            => $this->primaryKey(),
            'employee_id'   => $this->integer()->comment("Связь на модуль Employee"),
            'flight_leg_id' => $this->integer()->comment("ай ди плеча"),
            'roster_id'     => $this->integer()->comment("табельный номер "),
            'pos_code'      => $this->string()->comment("должность"),
            'id_dhd'        => $this->string(),
            'pos_leg1'      => $this->string()->comment("тех номер на первом плече"),
            'pos_flight'    => $this->string()->comment("тех номер установленный "),
            
            'created_at'    =>  $this->timestamp()->defaultValue(null),
            'updated_at'    =>  $this->timestamp()->defaultValue(null),
            
        ]);

    }

    
    public function safeDown()
    {
        $this->dropTable('ccp_flight_leg_crew');
        return true;
    }

}
