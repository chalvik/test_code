<?php
namespace common\modules\ccpFlight\tasks;

use common\modules\ccpFlight\models\console\FlightRefusalFoodParse;
use common\modules\scheduler\components\AbstractTask;
use common\modules\ccpFlight\models\console\FlightParse;
use yii\base\Exception;

/**
 * Задача для планировщика (модуль сommon/modules/scheduler )
 * Цель задачи  - обновление рейсов на 3 дня вперед (обновление ростера, или расписания)
 *
 * минимальный расчетный период обновление ... 10 минут
 * Class TaskFlightSheduledFor3day
 * @package common\modules\ccpFlight\tasks
 */
class TaskFlightRefusalFood extends AbstractTask
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $before = 2*60*60;
        $after = 1*60*60;
        $flights = FlightParse::getFlightsForUpdating($after, $before);
        $count = count($flights);
        $process = 100;
        $i = 1;

        try {
            /** @var FlightParse $flight */
            foreach ($flights as $flight) {

//            $flight = Flight::find()->where([
//                'id' => 431430
//            ])->one();
                $this->status = FlightRefusalFoodParse::UpdateForFlight($flight);
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
