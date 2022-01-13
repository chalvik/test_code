<?php

namespace common\modules\ccpFlight\migrations;

use common\modules\cargo\models\FlightMail;
use yii\db\Migration;

class m171006_135615_change_flight_mail_table extends Migration
{

    public function safeUp()
    {
        FlightMail::deleteAll();

        $this->dropColumn('{{%flight_mail}}', 'receptionId');
        $this->dropColumn('{{%flight_mail}}', 'departure_airport_id');
        $this->dropColumn('{{%flight_mail}}', 'arrival_airport_id');
        $this->dropColumn('{{%flight_mail}}', 'carrier');
        $this->dropColumn('{{%flight_mail}}', 'flight_flt');
        $this->dropColumn('{{%flight_mail}}', 'std');

        $this->addColumn('{{%flight_mail}}', 'flight_id', $this->integer());
        $this->addForeignKey('{{%flight_mail_fk}}', '{{%flight_mail}}', 'flight_id', '{{%ccp_flight}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        FlightMail::deleteAll();

        $this->addColumn('{{%flight_mail}}', 'receptionId', $this->integer());
        $this->addColumn('{{%flight_mail}}', 'departure_airport_id', $this->integer());
        $this->addColumn('{{%flight_mail}}', 'arrival_airport_id', $this->integer());
        $this->addColumn('{{%flight_mail}}', 'carrier', $this->integer());
        $this->addColumn('{{%flight_mail}}', 'flight_flt', $this->string());
        $this->addColumn('{{%flight_mail}}', 'std', $this->timestamp());

        $this->dropColumn('{{%flight_mail_fk}}', '{{%flight_mail}}');
        $this->dropColumn('{{%flight_mail}}', 'flight_id');
    }

}
