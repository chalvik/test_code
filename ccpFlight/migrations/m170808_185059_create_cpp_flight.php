<?php
namespace common\modules\ccpFlight\migrations;

use yii\db\Migration;

class m170808_185059_create_cpp_flight extends Migration
{
    public function safeUp()
    {
        
        $this->createTable('ccp_flight', [
            
            'id'                 => $this->primaryKey(),
            'day'                => $this->integer()->comment("дата aims"),            
            'flt'                => $this->integer()->comment("Номера рейса"),
            'fltDes'             => $this->string()->comment("описание рейса"),
            'os'                 => $this->string(2)->comment("литера"),
            'carrier'            => $this->integer()->comment("Id компании"),
            'aircraft_id'        => $this->integer()->comment("ай ди судна из справочника local"),
            'dep_airport_id'     => $this->integer()->comment("Аэропорт отправления"),
            'arr_airport_id'     => $this->integer()->comment("Аэропорт прибытия"),
            'std'                => $this->timestamp()->defaultValue(null)->comment("Плановое время начала движения"),
            'sta'                => $this->timestamp()->defaultValue(null)->comment("Плановое время окончание движения"),
            'etd'                => $this->timestamp()->defaultValue(null)->comment("Расчетное время начала движения"),
            'eta'                => $this->timestamp()->defaultValue(null)->comment("Расчетное время окончания движения"),
            'arr_gate'           => $this->string()->comment("Гейт прилета"),
            'dep_gate'           => $this->string()->comment("Гейт вылета"),
            'arr_weather'        => ' json ',
            'dep_weather'        => ' json ',
            'deleted'            => $this->boolean()->defaultValue(false)->comment("Флаг удален ли рейс"),
            'deleted_at'         => $this->timestamp()->defaultValue(null),
            'deleted_user_id'	 => $this->integer()->comment("Пользователь который удалил"),
            'canceled'           => $this->integer()->comment("идентификатор отмены рейса"),   
            'updated_user_id'    =>  $this->integer()->comment("User update flight"),
            'created_at'         =>  $this->timestamp()->defaultValue(null),
            'updated_at'         =>  $this->timestamp()->defaultValue(null),
            
        ]);

    }

    
    public function safeDown()
    {
        $this->dropTable('ccp_flight');
        return true;
    }

   
}
