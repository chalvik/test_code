<?php
namespace common\modules\ccpEmployee\tasks;

use common\modules\ccpAirport\models\Airport;
use common\modules\scheduler\components\AbstractTask;
use common\modules\ccpEmployee\models\console\EmployeeParse;

/**
 * Задача для планировщика (модуль сommon/modules/scheduler )
 * Цель задачи  - обновление базы сотрудников
 * минимальный расчетный период обновление ... 5 минут
 *
 * Class TaskEmployee
 * @package common\modules\ccpEmployee\tasks
 */
class TaskEmployee extends AbstractTask
{

    /**
     * @inheritdoc
     */
    public function run()
    {
        $psize = 200;
        $count = EmployeeParse::getCountEmployeeFromOracle();
        $pages = ceil($count/$psize);
        $this->status = true;

        for ($page = 0; $page <= $pages; $page++) {
            $data_employees = EmployeeParse::getEmployeeFromOracle($psize, $page*$psize);
            foreach ($data_employees as $data) {
                $airport = Airport::find()
                    ->where([
                    'iata' => trim($data['PORT_BASE'])
                    ])
                    ->one();


                $transaction = EmployeeParse::getDb()->beginTransaction();
                try {
                    $employee = EmployeeParse::findOne(['roster_id' => $data['ROSTER_ID']]);

                    if (!$employee) {
                        $employee = new EmployeeParse();
                    }

                    $crewtaidx = explode(',', $data['CREWCATIDX']);
                    if (is_array($crewtaidx)) {
                        /**
                         * @var  $crewtaidx array
                         */
                        foreach ($crewtaidx as $key => $value)
                        {
                            if ($value) {
                                $crewtaidx[$key] = (int)$value;
                            }
                        }
                    }

                    $employee->roster_id = $data['ROSTER_ID'];
                    $employee->name = $data['ROSTER_NAME'];
                    $employee->fio_eng = $data['FIO_ENG'];
                    $employee->fio_rus = $data['FIO_RUS'];
                    $employee->quals_list = $data['QUALS_LIST'];
                    $employee->langs_list = $data['LANGS_LIST'];
                    $employee->port_base = $data['PORT_BASE'];
                    $employee->port_base_id = isset($airport->id)?$airport->id:0;
                    $employee->last_updated_at = gmdate("Y-m-d H:i:s");
                    $employee->crewcatidx = $crewtaidx;


                    if (!$employee->save()) {
                        throw new \Exception(json_encode($employee->errors));
                    }

                    $transaction->commit();
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    $this->status = false;
                    $this->addErrors('exception', $e->getMessage());
                }
            }

            $process = floor($page/$pages*100);
            $this->saveLastActivity($process);
        }
    }
}
