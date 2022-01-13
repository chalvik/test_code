<?php

namespace common\modules\ccpFlight\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `cargo_mail`.
 */
class m171002_194444_create_cargo_mail_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%flight_mail}}', [
            'id' => $this->primaryKey(),
            'type' => $this->string(),
            'receptionId' => $this->integer(),
            'name' => $this->string(),
            'departure_airport_id' => $this->integer(),
            'arrival_airport_id' => $this->integer(),
            'flight_flt' => $this->integer(),
            'carrier' => $this->integer(),
            'std' => $this->timestamp(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%flight_mail}}');
    }
}
