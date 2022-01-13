<?php
namespace common\modules\ccpEmployee\models\console;

use common\modules\ccpEmployee\models\Employee;
use yii\db\ActiveRecord;

/**
 * Class EmployeeParse
 * @package common\modules\ccpEmployee\models\console
 */
class EmployeeParse extends Employee
{
    //put your code here

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'value' => gmdate("Y-m-d H:i:s"),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }


    /**
     * Get  Employee  array data records for $where  from Oracle (Aims)
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public static function getEmployeeFromOracle($limit = 200, $offset = 0)
    {
        $crew = (new \yii\db\Query())
            ->from(['t'=>'S7_IT.CCP#V_CREWDB'])
            ->limit($limit)
            ->offset($offset)
            ->all(\Yii::$app->dbAmis);
        
        return $crew;
    }
   
    /**
     *  Get  Count Employee  array data records for $where  from Oracle (Aims)
     * @return int|string
     */
    static function getCountEmployeeFromOracle()
    {
        $count = (new \yii\db\Query())
            ->from(['t'=>'S7_IT.CCP#V_CREWDB'])
            ->count('*', \Yii::$app->dbAmis);
        return $count;
    }
}
