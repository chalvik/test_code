<?php
namespace common\modules\ccpFlight\tasks;

use common\modules\ccpFlight\models\console\FlightParse;
use common\modules\scheduler\components\AbstractTask;
use common\modules\scheduler\models\SchedulerTaskRun;
use yii\base\Exception;

/**
 * Задача для планировщика (модуль сommon/modules/scheduler )
 * Цель задачи  - обновление экипажа рейса на 3 дня вперед
 * минимальный расчетный период обновление ... 10 минут
 *
 * Class TaskFlightCrewSheduledFor3day
 * @package common\modules\ccpFlight\tasks
 */
class TaskFlightCrewSheduledFor3day extends AbstractTask
{

    /**
     * @inheritdoc
     */
    public function run()
    {
        $before = 4*24*60*60;
        $after = -(260*60+1);
        $flights = FlightParse::getFlightsForUpdating($after, $before);
        $count = count($flights);
        $process = 0;
        $i = 1;

        try {
            /** @var FlightParse $flight */
            foreach ($flights as $flight) {
                if (time() > strtotime($this->task->last_run_at.' UTC') + SchedulerTaskRun::TIME_OUT_LAST_RUN) {
                    throw new Exception("Time out task . It was run 30 minutes ago");
                }
                $this->status = $flight->updateCrew();
                $process = floor($i/$count*100);
                $this->saveLastActivity($process);
                $i++;
            }
        } catch (Exception $e) {
            $this->addErrors('exception', $e->getMessage()." process = $process%");
            $this->status = false;
        }
    }
}
