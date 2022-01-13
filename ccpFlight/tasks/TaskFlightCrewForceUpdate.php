<?php

namespace common\modules\ccpFlight\tasks;

use common\modules\ccpFlight\models\CcpFlightForceUpdate;
use common\modules\ccpFlight\models\Flight;
use common\modules\scheduler\components\AbstractTask;
use common\modules\ccpFlight\models\console\FlightParse;
use common\modules\scheduler\models\SchedulerTaskRun;
use yii\base\Exception;

/**
 * Задача для планировщика (модуль сommon/modules/scheduler )
 * Цель задачи  - обновление рейсов на 3 дня вперед (обновление ростера, или расписания)
 *
 * минимальный расчетный период обновление ... 10 минут
 * Class TaskFlightSheduledFor3day
 * @package common\modules\ccpFlight\tasks
 */
class TaskFlightCrewForceUpdate extends AbstractTask
{
    /**
     * @inheritdoc
     */
    public function run()
    {

        $flights = FlightParse::find()->where(
            ['id' => CcpFlightForceUpdate::find()->select('flight_id')->where(['is_updated' => false])]
        )->all();
        $count = count($flights);
        $process = 0;
        $i = 1;

        try {
            /** @var FlightParse $flight */
            foreach ($flights as $flight) {

                $this->status = $flight->updateCrew();
                $flightForceUpdate = CcpFlightForceUpdate::findOne(['flight_id' => $flight->id]);
                $flightForceUpdate->is_updated = $this->status;
                $flightForceUpdate->updated_at = gmdate("Y-m-d H:i:s");
                $flightForceUpdate->update(false);
                $process = floor($i / $count * 100);
                $this->saveLastActivity($process);
                $i++;
            }
        } catch (Exception $e) {
            $this->addErrors('exception', $e->getMessage() . " process = $process%");
            $this->status = false;
        }
    }
}
