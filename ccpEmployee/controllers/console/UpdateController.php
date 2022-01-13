<?php

namespace common\modules\ccpEmployee\controllers\console;

use Yii;
use console\controllers\BaseController;
use common\modules\ccpEmployee\models\Employee;

/**
 * This is the controller class for module Employee.
 * Console commands.
 * Class UpdateController
 * @package common\modules\ccpEmployee\controllers\console
 */
class UpdateController extends BaseController
{
   /**
    * Update or Add Flight? FlightLeg, Crew and Passenger  in local database
     */
    public function actionIndex()
    {
        $this->log("Roster Crew Update)", "purple");
        $psize = 200;
        $count = $this->getCountEmployeeFromOracle();
        $pages = ceil($count/$psize);
        
        for ($page = 0; $page<=$pages; $page++) {
            $this->log("page $page from $pages", "yellow");
            $data_employees = $this->getEmployeeFromOracle($psize, $page*$psize);
            $transaction = Employee::getDb()->beginTransaction();
            try {
                foreach ($data_employees as $data) {
                    $employee = Employee::findOne(['roster_id'=>$data['ROSTER_ID']]);
                    if (!$employee) {
                        $employee = new Employee();
                    }
                    
                    $employee->roster_id        =   $data['ROSTER_ID'];
                    $employee->name             =   $data['ROSTER_NAME'];
                    $employee->fio_eng          =   $data['FIO_ENG'];
                    $employee->fio_rus          =   $data['FIO_RUS'];
                    $employee->quals_list       =   $data['QUALS_LIST'];
                    $employee->langs_list       =   $data['LANGS_LIST'];
                    $employee->port_base        =   $data['PORT_BASE'];
                    $employee->last_updated_at  =   gmdate("Y-m-d H:i:s");
//                    $employee->block_hours	 = $data['BLOCK_HOURS'];
                    $employee->save();
                }
                
                $transaction->commit();
            } catch (\Exception $e) {
                print_r($e->getMessage());
                $transaction->rollBack();
                echo $this->log("-", "yellow");
            }
        }
    }
    
    /**
     * Get  Employee  array data records for $where  from Oracle (Aims)
     * @param int $limit
     * @param int $offset
     * @return array
     */
    private function getEmployeeFromOracle($limit = 200, $offset = 0)
    {
        $crew = (new \yii\db\Query())
            ->from(['t'=>'S7_IT.CCP#V_CREWDB'])
            ->limit($limit)
            ->offset($offset)
            ->all(\Yii::$app->dbAmis);
        return $crew;
    }

    /**
     * Get  Count Employee  array data records for $where  from Oracle (Aims)
     */
    private function getCountEmployeeFromOracle()
    {
        $count = (new \yii\db\Query())
            ->from(['t'=>'S7_IT.CCP#V_CREWDB'])
            ->count('*', \Yii::$app->dbAmis);
        return $count;
    }

    /**
     * Truncate table ccp_employee
     */
    public function actionTruncate()
    {
        $table = Employee::tableName();
        Yii::$app->db->createCommand()->truncateTable($table)->execute();
    }
}