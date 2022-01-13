<?php

namespace common\modules\ccpFlight\tasks\helpers;

use common\models\helpers\TimeHelper;
use common\modules\ccpFlight\models\Flight;
use common\modules\ccpFlight\models\helpers\HelperFlightFeatures;
use common\modules\ccpFlight\models\helpers\HelperFlightFeaturesHandler;
use common\modules\scheduler\components\AbstractTask;
use common\modules\scheduler\models\ExtendedLogger;

/**
 * Задача для планировщика (модуль сommon/modules/scheduler )
 * Цель задачи  - обновление рейсов до вылета с момента вылета до 2 часов до вылета
 * минимальный расчетный период обновление ... 10 минут
 *
 * Class TaskHelpFlightFeatures
 * @package common\modules\ccpFlight\tasks
 */

class TaskHelpFlightFeatures extends AbstractTask
{

    const TIME_CHECK_FROM = -10;  //hours
    const TIME_CHECK_TO = 1.4;  //hours
    const LIMIT = 10;  //hours

    /**
     * @return []|\common\modules\ccpFlight\models\Flight
     */
    private function getFLights()
    {
        return Flight::getCurrentFlights(self::TIME_CHECK_TO, self::TIME_CHECK_FROM)
            ->andWhere(['NOT IN', 'id', HelperFlightFeatures::find()->select('flight_id')->andWhere(['>=', 'created_at', TimeHelper::getGreenwichTime(strtotime(' - 5 hours'))])])
            ->limit(self::LIMIT)
            ->all();
    }


    /**
     * @inheritdoc
     */
    public function run()
    {
        try {
            $process = 0;
            if ($flights = $this->getFLights()) {
                $count = count($flights);
                foreach ($flights as $key => $flight) {
                    ExtendedLogger::storeLog("CHECK FLIGHT " . $flight->flt . " - STD=" . $flight->std);
                    $handler = new HelperFlightFeaturesHandler($flight);
                    $handler->handle();
                    ExtendedLogger::storeLog(HelperFlightFeatures::find()->where(['flight_id' => $flight->id])->asArray()->all());
                    if ($this->task) $this->saveLastActivity(round($key / $count * 100));
                }
            } else {
                ExtendedLogger::storeLog("NO FLIGHTS");

            }
            $this->status = true;
        } catch (\Exception $e) {
            $this->status = false;
            $this->addErrors('exception', $e->getTraceAsString() . " process = $process%");
        }
    }
}
