<?php

namespace common\modules\ccpFlight\migrations;

use yii\db\Migration;

class m170816_145457_flight_food_changed_relation extends Migration
{

    const UNWANTED_TABLE_NAME = "{{%flight_food_menu}}";
    const TABLE_NAME = "{{%ccp_flight}}";
    const COLUMN = "food_menu_id";

    const REF_TABLE = "{{%food_menu}}";
    const REF_COLUMN = "id";

    public function safeUp()
    {
        $this->dropTable(self::UNWANTED_TABLE_NAME);
        $this->addColumn(self::TABLE_NAME, self::COLUMN, $this->smallInteger());

        $this->createIndex("food_menu_index", self::TABLE_NAME, self::COLUMN);
        $this->addForeignKey(
            "flight_food_menu_fk",
            self::TABLE_NAME,
            self::COLUMN,
            self::REF_TABLE,
            self::REF_COLUMN
        );
    }

    public function safeDown()
    {
        $this->dropColumn(self::TABLE_NAME, self::COLUMN);
        $this->createTable(self::UNWANTED_TABLE_NAME, [
            'flight_id' => $this->smallInteger(),
            'food_menu_id' => $this->smallInteger(),
        ]);
    }

}
