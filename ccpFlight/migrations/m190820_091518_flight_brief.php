<?php

namespace common\modules\ccpFlight\migrations;

use yii\db\Migration;

/**
 * Class m190820_091518_flight_brief
 */
class m190820_091518_flight_brief extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable('{{%ccp_flight_briefings}}', [
            'id'             => $this->primaryKey(),
            'title'             => $this->string(256),
            'std'       => $this->timestamp()->comment("Std"),
            'flt'       => $this->integer()->comment("Flt"),
            'flight_id'       => $this->integer()->comment("Flight id"),
            'user_id'       => $this->integer()->null()->comment("Пользователь"),
            'roster_id'       => $this->integer()->null()->comment("Табельный"),
            'file_id'     => $this->integer()->null()->comment("Файл "),
            'status'     => $this->integer()->null()->comment("Статус"),
            'created_at'    => $this->timestamp()->defaultValue(null),
            'updated_at'    => $this->timestamp()->defaultValue(null)

        ]);

        $this->createIndex("idx-flight_brief-flight_id",'{{%ccp_flight_briefings}}','flight_id',false);
        $this->createIndex("idx-flight_brief-flt",'{{%ccp_flight_briefings}}','flt',false);
        $this->createIndex("idx-flight_brief-std",'{{%ccp_flight_briefings}}','std',false);


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropTable('{{%ccp_flight_briefings}}');

    }

}
