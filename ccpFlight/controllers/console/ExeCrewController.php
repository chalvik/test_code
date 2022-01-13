<?php
namespace common\modules\ccpFlight\controllers\console;

use common\modules\ccpFlight\exceptions\FlightException;
use common\modules\ccpFlight\models\console\FlightLegParse;
use common\modules\ccpFlight\models\console\FlightParse;
use console\controllers\BaseController;
use Yii;

/**
 * Консольные команды для обновления экипажа рейсов
 * This is the controller class for module  Flight.
 * Console commands.
 * Class CronController
 * @package common\modules\ccpFlight\controllers\console
 */
class ExeCrewController extends BaseController
{

    /**
     * Обновляет экипаж с учетов входных данных введенных с консоли
     * @param int $display_log
     * @return null
     */
    public function actionUpdate($display_log = self::LOG_DISPLAY)
    {
        $select = $this->inputWithConsole("1 - Один день , 2 - Период ");
        switch ($select) {
            case 1:
                $this->log("Обновление экипажа рейса(ов) для одного дня", 'yellow', null, $display_log);
                $this->log("Ввод номера рейса", 'yellow', null, $display_log);
                $flight = $this->inputWithConsoleFlight();
                $this->log("Ввод даты ", 'yellow', null, $display_log);
                $aims_date = $this->inputWithConsoleAimsDay();
                if ($aims_date) {
                    $where['t.DAY'] = $aims_date;
                } else {
                    $this->log('Ошибка ввода информации для ', "red", null, $display_log);
                    return null;
                }

                if ($flight) {
                    $where['t.FLT'] = $flight;
                }
                break;
            case 2:
                $this->log("Обновление рейса(ов) для периода", 'yellow', null, $display_log);
                $this->log("Ввод номера рейса", 'yellow', null, $display_log);
                $flight = $this->inputWithConsoleFlight();
                $this->log("Ввод начала периода ", 'yellow', null, $display_log);
                $aims_date_start = $this->inputWithConsoleAimsDay();
                $this->log("Ввод конца периода ", 'yellow', null, $display_log);
                $aims_date_end = $this->inputWithConsoleAimsDay();
                if ($aims_date_start && $aims_date_end) {
                    $where[] = "(t.DAY BETWEEN $aims_date_start AND $aims_date_end)";
                } else {
                    $this->log('Ошибка ввода информации для ', "red", null, $display_log);
                    return null;
                }
                if ($flight) {
                    $where['t.FLT'] = $flight;
                }
                break;
            case 3:
                $this->log("Обновление экипажа для одного рейса", 'yellow', null, $display_log);
                $this->log("Ввод номера рейса", 'yellow', null, $display_log);
                $flight = $this->inputWithConsoleFlight();
                if ($flight) {
                    $where['t.FLT'] = $flight;
                } else {
                    $this->log("Ошибка ввода номера рейса   ", 'red', null, $display_log);
                    return null;
                }
                break;
            default:
                $this->log("Ошибка выбора   ", 'red', null, $display_log);
                return null;
                break;
        }

          //   $this->updateForWhereOracle($where);
    }

    /**
     * Обновляет экипаж для одного рейса
     * write message to console
     *
     * @param FlightParse $flight
     */
    private function saveCrew($flight)
    {
        $leg = $flight->firstLeg;
        $where = [
            't.DAY'        =>  $leg->day,
            't.CARRIER'    =>  $leg->carrier,
            't.FLT'        =>  $leg->flt,
//         't.DEP'        =>  trim($leg->dep)
        ];

        $crew_arr = $this->getCrewFromOracle($where);
        foreach ($crew_arr as $crew_data) {
            $crew = FlightLegCrew::find()
                ->where([
                    'flight_id' => $flight->id,
                    'roster_id' => $crew_data['ROSTER_ID']
                ])
                ->one();
            if (!$crew) {
                $crew = new FlightLegCrew();
            }
            $crew->flight_id  = $flight->id;
            $crew->roster_id  = $crew_data['ROSTER_ID'];
            $crew->pos_code   = $crew_data['POS_CODE'];
            $crew->id_dhd     = $crew_data['ID_DHD'];
            $crew->pos_leg1   = $crew_data['POS_LEG_1'];

            if ($crew->save()) {
               // To Do
            }
        }
    }

    /**
     * Get  Crew  array data records for $where  from Oracle (Aims)
     * @param $where
     * @param int $limit
     * @param int $offset
     * @return array
     */
    private function getCrewFromOracle($where, $limit = 25, $offset = 0)
    {
        $crew = (new \yii\db\Query())
            ->from(['t'=>'S7_IT.CCP#V_ROSTER'])
            ->where($where)
            ->limit($limit)
            ->offset($offset)
            ->all(\Yii::$app->dbAmis);
        return $crew;
    }


}

















//
//
//    /**
//     *  попробовать Переделать  !!!!  Медленно работает
//     * 1 - запрос в oracle  по date aims
//     * 2 потом обновить относительно fight
//     * @param $date
//     * @param int $days
//     * @param int $display_log
//     */
//    public function actionUpdateCrew($date, $days = 1, $display_log = BaseController::LOG_NONE)
//    {
//        $errors =[];
//        $start  = Aims::DateToAimsDate($date);
//        $end    = $start+$days-1;
//
//        $this->log('Update Flight and Legs', "purple", null, $display_log);
//        $this->log('start day : '.$date, "blue", null, $display_log);
//        $this->log('start aims day : '.$start, "blue", null, $display_log);
//        $this->log('end aims day : '.$end, "blue", null, $display_log);
//
//        if ($end >= $start) {
//            for ($aimsday=$start; $aimsday<=$end; $aimsday++) {
//                $flights = FlightParse::find()
//                        ->where([
//                            'day' => $aimsday
//                        ])
//                        ->all();
//                foreach ($flights as $flight) {
//                    $result = $flight->updateCrew();
//                    if ($result) {
//                         echo('+');
//                    } else {
//                         echo('-');
//                    }
//                }
//                 echo('\n\r');
//            }
//        }
//    }
//
//
//    /**
//     * Update or Add Flight? FlightLeg, Crew and Passenger  in local database fronm Cron
//     * @param $time
//     * @param $date
//     * @param null $offset
//     * @param int $display_log
//     */
//    public function actionUpdateTime($time, $date, $offset = null, $display_log = BaseController::LOG_NONE)
//    {
//        $this->log('Update Flight $time minute ', "purple", null, $display_log);
//        if ($date) {
//            $tstart = ($offset)?strtotime($date)+$offset*60 :strtotime($date);
//        } else {
//            $tstart = ($offset)?(time()+$offset*60):time();
//            $date    = gmdate("Y-m-d");
//        }
//
//        $start   = gmdate("Y-m-d H:i", $tstart);
//        $end     = gmdate("Y-m-d H:i", $tstart+(int)$time*60);
//
//        $errors =[];
//        $startAims     = Aims::DateToAimsDate($start);
//
//        $this->log('Update Flight and Legs for Time Update', "purple", null, $display_log);
//        $this->log('start : '.$start, "blue", null, $display_log);
//        $this->log('end : '.$end, "blue", null, $display_log);
//
//        $where = "STD_DATE BETWEEN TO_DATE('$start', 'yyyy-mm-dd hh24:mi') AND TO_DATE ('$end', 'yyyy-mm-dd hh24:mi')" ;
//
//        $psize = 100;
//        $count = FlightLegParse::getCountLegsFromOracle($where);
//        $page = 0;
//        $pages = ceil($count/$psize);
//
//        $this->log('count legs ='.$count, "yellow", null, $display_log);
//
//        for ($page = 0; $page < $pages; $page++) {
//            $alegs = FlightLegParse::getLegsFromOracle($where, $psize, $page*$psize);
//            $errors = $this->UpdateFlightesFromArray($alegs, $display_log);
//            if (! $errors) {
//                $this->log('+', 'green', null, $display_log);
//            } else {
//                $this->log(implode($errors, "/n/r"), 'green', null, $display_log);
//            }
//        }
//
////            FlightParse::UpdateFromLegs($aimsday);
//    }
//
//    /**
//     * Update Crew from interval
//     * @param $time
//     * @param $date
//     * @param $offset
//     * @param int $display_log
//     */
//    public function actionCrewUpdateTime($time, $date, $offset, $display_log = BaseController::LOG_NONE)
//    {
//
//        if ($date) {
//            $tstart = ($offset)?strtotime($date)+$offset*60 :strtotime($date);
//        } else {
//            $tstart = ($offset)?(time()+$offset*60):time();
//        }
//
//        $start   = gmdate("Y-m-d H:i:s", $tstart);
//        $end     = gmdate("Y-m-d H:i:s", $tstart+(int)$time*60);
//
//        $this->log('Update Flight Crew ', "purple", null, $display_log);
//        $this->log('start  : '.$start, "blue", null, $display_log);
//        $this->log('end : '.$end, "blue", null, $display_log);
//
//        $flights = FlightParse::find()
//            ->where(['BETWEEN','std',$start,$end])
//            ->all();
//
//        foreach ($flights as $flight) {
//            $result = $flight->updateCrew();
//            $result = "FlightCrew : ".implode(" >> ", $result);
//            $this->log("crew flight_id: $flight->id - $result", "blue", null, $display_log);
//        }
//    }
//
//    /**
//     * Update or Create Flight and Legs  for array legs from oracle
//     *
//     * @param array $alegs
//     * @param integer $display_log
//     * @return array
//     * @throws FlightException
//     */
//    private function UpdateFlightesFromArray($alegs, $display_log = BaseController::LOG_NONE)
//    {
//        $errors =[];
//        $transaction = FlightLegParse::getDb()->beginTransaction();
//        try {
//            foreach ($alegs as $leg) {
//                $flight = FlightParse::saveFlight($leg, $isnewflight);
//                $result = FlightLegParse::saveFlightLeg($leg, $flight, $isnewleg);
//                if ($result) {
//                    if ($display_log == BaseController::LOG_DISPLAY) {
//                        echo "*";
//                    }
//                } else {
//                    if ($display_log == BaseController::LOG_DISPLAY) {
//                        echo "-";
//                    }
//                    throw new FlightException("Error save leg ");
//                }
//            }
//
//            $transaction->commit();
//
//        } catch (\Exception $e) {
//            $errors[] = $e->getMessage();
//            $transaction->rollBack();
//        }
//        return $errors;
//    }
//
//    /**
//     * Clear table flight and flight_leg and reset SEQUENCE
//     */
//    public function actionTruncate()
//    {
//        $table = FlightParse::tableName();
//        Yii::$app->db->createCommand()->truncateTable($table)->execute();
//        Yii::$app->db->createCommand()->resetSequence($table);
//        $table = FlightLegParse::tableName();
//        Yii::$app->db->createCommand()->truncateTable($table)->execute();
//        Yii::$app->db->createCommand()->resetSequence($table);
//    }
//
//    /**
//     * Truncate table crew
//     */
//    public function actionTruncateCrew()
//    {
//        $table = FlightCrewParse::tableName();
//        Yii::$app->db->createCommand()->truncateTable($table)->execute();
//        Yii::$app->db->createCommand()->resetSequence($table);
//    }
//
//    /**
//     * Test
//     */
//    public function actionTest()
//    {
//        $flights = FlightParse::getFlightsForUpdating();
//        $this->log(reset($flights)->std, 'yellow', null, BaseController::LOG_DISPLAY);
//        $this->log(end($flights)->std, 'yellow', null, BaseController::LOG_DISPLAY);
//        $count = count($flights);
//        $this->log($count, 'green', null, BaseController::LOG_DISPLAY);
//    }
//}
