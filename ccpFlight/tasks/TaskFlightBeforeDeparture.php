<?php
namespace common\modules\ccpFlight\tasks;

use common\modules\ccpFlight\models\console\FlightParse;
use common\modules\ccpFlight\models\console\FlightLegParse;
use common\modules\scheduler\components\AbstractTask;

/**
 * Задача для планировщика (модуль сommon/modules/scheduler )
 * Цель задачи  - обновление рейсов до вылета с момента вылета до 2 часов до вылета
 * минимальный расчетный период обновление ... 10 минут
 *
 * Class TaskFlightBeforeDeparture
 * @package common\modules\ccpFlight\tasks
 */
class TaskFlightBeforeDeparture extends AbstractTask
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $offset = 0;
        $count_day = 2*60*60;   // day
        $current_time = time();
        $tstart = ($offset)?$current_time+$offset :$current_time;
        $start = gmdate("Y-m-d H:i", $tstart);
        $end = gmdate("Y-m-d H:i", $tstart+$count_day);

        $where = "STD_DATE BETWEEN TO_DATE('$start', 'yyyy-mm-dd hh24:mi') AND TO_DATE ('$end', 'yyyy-mm-dd hh24:mi')" ;
       // $where .= "AND CANCELED = 0";

        $psize = 200;
        $count = FlightLegParse::getCountLegsFromOracle($where);
        $pages = ceil($count/$psize);
        $process = 0;

        try {
            for ($page = 0; $page < $pages; $page++) {
                $alegs = FlightLegParse::getLegsFromOracle($where, $psize, $page*$psize);

                foreach ($alegs as $leg) {
                    $result = FlightParse::saveFlight($leg);
                }
                $process = floor($page/$pages*100);
                $this->saveLastActivity($process);
            }
            $this->status = true;
        } catch (\Exception $e) {
            $this->status = false;
            $this->addErrors('exception', $e->getMessage()." process = $process%");
        }
    }
}