<?php

namespace common\modules\ccpFlight\migrations;

use common\modules\cargo\models\FlightLoadCompartment;
use common\modules\cargo\models\FlightLoadDetail;
use common\modules\cargo\models\FlightMail;
use common\modules\ccpFlight\models\Flight;
use yii\db\Migration;

/**
 * Class m190820_091518_flight_brief
 */
class m191029_091518_add_primary_keys extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $count = FlightLoadDetail::find()->count();
        $this->dropPrimaryKey('flight_load_details_pk', '{{%flight_load_details}}');
        $this->execute(' ALTER TABLE flight_load_details ADD COLUMN id SERIAL PRIMARY KEY;');
        $this->execute('ALTER SEQUENCE flight_load_details_id_seq RESTART WITH ' . ($count + 1));


        $count = FlightLoadCompartment::find()->count();

        $this->dropPrimaryKey('flight_load_compartment_pk', '{{%flight_load_compartment}}');
        $this->execute(' ALTER TABLE flight_load_compartment ADD COLUMN id SERIAL PRIMARY KEY;');
        $this->execute('ALTER SEQUENCE flight_load_compartment_id_seq RESTART WITH ' . ($count + 1));


        $count = FlightMail::find()->count();
        $this->dropPrimaryKey('flight_mail_pk', '{{%flight_mail}}');
        $this->execute(' ALTER TABLE flight_mail ADD COLUMN id SERIAL PRIMARY KEY;');
        $this->execute('ALTER SEQUENCE flight_mail_id_seq RESTART WITH ' . ($count + 1));


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropColumn('{{%flight_load_details}}', 'id');
        $this->addPrimaryKey('flight_load_details_pk', '{{%flight_load_details}}', ['type', 'flight_id', 'unit']);


        $this->dropColumn('{{%flight_load_compartment}}', 'id');
        $this->addPrimaryKey('flight_load_compartment_pk', '{{%flight_load_compartment}}', ['flight_id', 'designator']);


        $this->dropColumn('{{%flight_mail}}', 'id');
        $this->addPrimaryKey('flight_mail_pk', '{{%flight_mail}}', ['flight_id', 'invoice_number']);


    }

}
