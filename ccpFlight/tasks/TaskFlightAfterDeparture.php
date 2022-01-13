<?php
namespace common\modules\ccpFlight\tasks;

use common\modules\ccpFlight\models\console\FlightLegParse;
use common\modules\ccpFlight\models\console\FlightParse;
use common\modules\scheduler\components\AbstractTask;
use yii\log\Logger;

/**
 * Задача для планировщика (модуль сommon/modules/scheduler )
 * Цель задачи  - обновление рейсов после посадки
 * минимальный расчетный период обновление ... 10 минут
 *
 * Class TaskFlightAfterDeparture
 * @package common\modules\ccpFlight\tasks
 */
class TaskFlightAfterDeparture extends AbstractTask
{
    const ACTIVITY_INIT = 0;
    const ACTIVITY_DONE = 100;
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        $result = [];
        $current_time = time();
        $start = gmdate("Y-m-d H:i", $current_time-60*60);
        $end   = gmdate("Y-m-d H:i", $current_time+20*60);
        $where = "BLON_DATE BETWEEN TO_DATE('$start', 'yyyy-mm-dd hh24:mi') AND TO_DATE ('$end', 'yyyy-mm-dd hh24:mi')" ;
        
       try {
            $this->saveLastActivity(static::ACTIVITY_INIT);
            
            $alegs = FlightLegParse::getLegsFromOracle($where);
            
            foreach ($alegs as $leg) {
                $result[] = FlightParse::saveFlight($leg);
            }

            $this->saveLastActivity(static::ACTIVITY_DONE);
            $this->status = true;
        } catch (\Exception $e) {
            $this->addErrors('exception', $e->getMessage());
            $this->status = false;
        }
    }
}
