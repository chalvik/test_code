<?php

    namespace common\modules\ccpFlight\migrations;

use yii\db\Migration;

/**
 * Class m200219_203934_flight_crew_refusal_food
 */
class m200219_203934_flight_crew_refusal_food extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable('{{%ccp_flight_refusal_food}}', [
            'id'             => $this->primaryKey(),
            'flight_id'      => $this->integer()->comment('Идентификатор рейса'),
            'roster_id'      => $this->string(256)->comment('crewMemberId или табельный номер'),
            'crewMemberFullName'=> $this->string(256)->comment('ФИО сотрудника'),
            'role'=> $this->string(256)->comment('Роль на рейсе'),
            'status'=> $this->string(256)->comment('Статус'),

            'created_at'    => $this->timestamp()->defaultValue(null),
            'updated_at'    => $this->timestamp()->defaultValue(null)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropTable('{{%ccp_flight_refusal_food}}');

    }

}
