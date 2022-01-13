<?php
namespace common\modules\ccpFlight\controllers\console;

use common\modules\ccpFlight\exceptions\FlightException;
use common\modules\ccpFlight\models\console\FlightLegParse;
use common\modules\ccpFlight\models\console\FlightParse;
use console\controllers\BaseController;
use Yii;

/**
 * Консольные команды для обновления рейсов и плечей рейсов
 * This is the controller class for module  Flight.
 * Console commands.
 * Class CronController
 * @package common\modules\ccpFlight\controllers\console
 */
class ExeController extends BaseController
{

    /**
     * Обновляет рейсы с учетов входных данных введенных с консоли
     * @param int $display_log
     * @return null
     */
    public function actionUpdate($display_log = self::LOG_DISPLAY)
    {
        $select = $this->inputWithConsole("1 - Один день , 2 - Период ");
        switch ($select) {
            case 1:
                $this->log("Обновление рейса(ов) для одного дня", 'yellow', null, $display_log);
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
            default:
                $this->log("Ошибка выбора   ", 'red', null, $display_log);
                return null;
                break;
        }

        $this->updateForWhereOracle($where);
    }

    /**
     * Отправляет запрос в базу Oracle , получает данные по плечам
     * и обновляет. создает рейсы используя полученные плечи
     * @param $where
     * @param $display_log
     */
    private function updateForWhereOracle($where, $display_log = self::LOG_DISPLAY)
    {
        $psize = 100;
        $count = FlightLegParse::getCountLegsFromOracle($where);
        $page = 0;
        $pages = ceil($count/$psize);
        $this->log('count legs ='.$count, "blue", null, $display_log);

        for ($page = 0; $page < $pages; $page++) {
            $alegs = FlightLegParse::getLegsFromOracle($where, $psize, $page * $psize);
            $errors = $this->UpdateFlightesFromArray($alegs, $display_log);
            if (!$errors) {
                $this->log('+', 'green', null, $display_log);
            } else {
                $this->log(implode($errors, "/n/r"), 'green', null, $display_log);
            }
        }
    }

    /**
     * Обновляет плечи рейса и сами рейсы с массива данных
     * плечей  полученного с внешнего сервиса
     *
     * @param array $alegs
     * @param integer $display_log
     * @return array
     * @throws FlightException
     */
    private function updateFlightesFromArray($alegs, $display_log = BaseController::LOG_NONE)
    {
        $errors =[];
        $transaction = FlightLegParse::getDb()->beginTransaction();
        try {
            foreach ($alegs as $leg) {
                $flight = FlightParse::saveFlight($leg);
                $result = FlightLegParse::saveFlightLeg($leg, $flight, $isnewleg);
                if ($result) {
                    if ($display_log == BaseController::LOG_DISPLAY) {
                        echo "*";
                    }
                } else {
                    if ($display_log == BaseController::LOG_DISPLAY) {
                        echo "-";
                    }
                    throw new FlightException("Error save leg ");
                }
            }

            $transaction->commit();

        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
            $transaction->rollBack();
        }
        return $errors;
    }

    /**
     * Удаление всех плечей и рейсов
     * Clear table flight and flight_leg and reset SEQUENCE
     */
    public function actionTruncate()
    {

        $table = FlightParse::tableName();
        Yii::$app->db->createCommand()->truncateTable($table)->execute();
        Yii::$app->db->createCommand()->resetSequence($table);
        $table = FlightLegParse::tableName();
        Yii::$app->db->createCommand()->truncateTable($table)->execute();
        Yii::$app->db->createCommand()->resetSequence($table);
    }


    /**
     * Удаление задублированных записей, через 1 день от текущего времени и позже
     * Clear table flight  for dublicate records
     */
    public function actionDeleteD()
    {

        $date = gmdate("Y-m-d H:i:s", strtotime('+ 3 hours'));
        $query = FlightParse::deleteAll("std > '$date'");
    }

    /**
     * Удаление задублированных записей, в введенный промежуток времени со связями
     * экипаж грузы
     * пассажиры
     * трансферники
     * сообщения и тикеты чата
     * Clear table flight  for dublicate records
     */
    public function actionDeleteWith()
    {

        $date = gmdate("Y-m-d H:i:s", strtotime('+ 1 day'));
        $query = FlightParse::deleteAll("std > '$date'");
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
