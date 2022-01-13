<?php
namespace common\modules\ccpFlight\migrations;


use yii\db\Migration;

class m170808_185108_create_cpp_flight_leg extends Migration
{
    public function safeUp()
    {
        
        $this->createTable('ccp_flight_leg', [
            
            'id'                => $this->primaryKey(),
            'flight_id'         => $this->integer()->comment("номер рейса"),
            'day'               => $this->integer()->comment("дата aims"),
            'flt'               => $this->integer()->comment("Номера рейса"),
            'dep'               => $this->string()->comment("Аэропорт отправления"),
            'dep_airport_id'    => $this->integer()->comment("Аэропорт отправления из справочника"),
            'carrier'           => $this->integer()->comment("компания"),
            'legcd'             => $this->string(),
            'arr'               => $this->string()->comment("Аэропорт прибытия"),
            'arr_airport_id'    => $this->integer()->comment("Аэропорт прибытия из справочника"),
            'ac'                => $this->string()->comment("тип воздушного судна из базы AIMS"),
            'reg'               => $this->string()->comment("рег номер борта"),
            'aircraft_id'       => $this->integer()->comment("ай ди судна из справочника"),
            'canceled'          => $this->boolean(),
            'adate'             => $this->string(),
            'aroute'            => $this->string(),
            'std'               => $this->timestamp()->defaultValue(null)->comment("Плановое время начала движения"),
            'sta'               => $this->timestamp()->defaultValue(null)->comment("Плановое время окончание движения"),
            'etd'               => $this->timestamp()->defaultValue(null)->comment("Расчетное время начала движения"),
            'eta'               => $this->timestamp()->defaultValue(null)->comment("Расчетное время окончания движения"),
            'blof'              => $this->timestamp()->defaultValue(null)->comment("время снятия с тормоза"),
            'tkof'              => $this->timestamp()->defaultValue(null)->comment("время отрыва от земли"),
            'tdown'             => $this->timestamp()->defaultValue(null)->comment("время касания земли"),
            'blon'              => $this->timestamp()->defaultValue(null)->comment("время постановки на тормоз"),
            'dep_gate'          => $this->string()->comment("gate аэропорта вылета"),
            'arr_gate'          => $this->string()->comment("gate аэропорта прилета"),
            
            'created_at'         =>  $this->timestamp()->defaultValue(null),
            'updated_at'         =>  $this->timestamp()->defaultValue(null),
            
        ]);

    }

    
    public function safeDown()
    {
        $this->dropTable('ccp_flight_leg');
        return true;
    }

}
