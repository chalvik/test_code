<?php
namespace common\modules\ccpEmployee\migrations;

use yii\db\Migration;

/**
 * Class m190404_074229_add_crewcatidx_employee
 */
class m190404_074229_add_crewcatidx_employee extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%ccp_employee}}', 'crewcatidx', "int[] DEFAULT '{}'");
    }

    public function safeDown()
    {
        $this->dropColumn('{{%ccp_employee}}', 'crewcatidx');
        return true;
    }
}
