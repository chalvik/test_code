<?php

namespace common\modules\ccpFlight\migrations;

use yii\db\Migration;

class m171029_173148_feature_flight extends Migration
{

    public function safeUp()
    {
        $this->createTable('{{%feature_flight}}', [
            'id' => $this->primaryKey(),
            'flight_flt' => $this->integer(),
            'note' => $this->text()->comment('Информация о совместной эксплуатации'),
            'start_date' => $this->timestamp(),
            'end_date' => $this->timestamp(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%feature_flight}}');
    }

}
