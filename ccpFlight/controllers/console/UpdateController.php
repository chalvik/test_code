<?php
namespace common\modules\ccpFlight\controllers\console;

use common\modules\ccpFlight\models\console\FlightLegParse;
use common\modules\ccpFlight\models\console\FlightParse;
use Yii;
use console\models\Aims;
use common\modules\ccpFlight\models\FlightLeg;
use common\modules\ccpFlight\models\Flight;
use common\modules\ccpFlight\models\FlightLegCrew;
use common\modules\ccpAircraft\models\Aircraft;
use common\modules\ccpAirport\models\Airport;
use console\controllers\BaseController;

/**
 * This is the controller class for module  Flight.
 * Console commands.
 * Class UpdateController
 * @package common\modules\ccpFlight\controllers\console
 */
class UpdateController extends BaseController
{


    /**
     * Update Flight and Crew from to date
     * @param $flight_id
     * @return bool
     */
    public function actionAll($display_log = self::LOG_DISPLAY)
    {
                $this->log("Обновление рейса(ов) для периода", 'yellow', null, $display_log);
                $this->log("Ввод начала периода ", 'yellow', null, $display_log);
                $start = $this->inputDay("Начало периода :" );
                $this->log("Ввод конца периода ", 'yellow', null, $display_log);
                $end = $this->inputDay(" Конец периода : ");

                $where = "DAY BETWEEN ". $start['aims']." AND ".$end['aims'] ;
//        $where = "STD_DATE BETWEEN TO_DATE('".$start['timestamp']."', 'yyyy-mm-dd hh24:mi') AND TO_DATE ('".$end['timestamp']."', 'yyyy-mm-dd hh24:mi')";

                $psize = 200;
                $count = FlightLegParse::getCountLegsFromOracle($where);
                $pages = ceil($count/$psize);
                $process = 0;

                $this->log("Count flight:".$count, 'green', null, $display_log);

                try {
                    for ($page = 0; $page < $pages; $page++) {
                        $alegs = FlightLegParse::getLegsFromOracle($where, $psize, $page*$psize);

                        foreach ($alegs as $leg) {
//                            print_r($leg);
                            $flight = FlightParse::saveFlight($leg);

//                            print_r($flight); die();
                            if ($flight) {
                                $flight->updateCrew();
                            } else {
                                $this->log(" Error update flight ".$leg['FLT'], 'red', null, $display_log);
                            }
                        }
                        $process = floor($page/$pages*100);
                        $this->log($process, 'green', null, $display_log);
                    }
                } catch (\Exception $e) {
                    $this->log($e->getMessage(), 'red', null, $display_log);
                }

    }


    /**
     * Update or Add Crew for FlightLeg in local database
     * @param $flight_id
     * @return bool
     */
    public function actionCrew($flight_id)
    {
        $flight = Flight::findOne($flight_id);
        if (!$flight) {
            $this->log("Flight not found ", "red");
            return false;
        }
        return $this->saveCrew($flight);
    }

    /**
     * Update or Add Flight in local database
     * @param $date
     * @param int $days
     */
    public function actionFlights($date, $days = 0)
    {
        $start = Aims::DateToAimsDate($date);
        $end = $start+$days;

        $this->log('start day ='.$date, "green");
        $this->log('start aims day ='.$start, "green");
        $this->log('end aims day ='.$end, "green");

        for ($aimsday = $start; $aimsday <= $end; $aimsday++) {
            $where = ["t.DAY" => $aimsday];
            $alegs = $this->getLegsFromOracle($where, 500, 0);
            $count_update_leg   = 0;
            $count_create_leg   = 0;
            $count_error_leg    = 0;
            $count_update_flight   = 0;
            $count_create_flight   = 0;
            $count_error_flight    = 0;
            $iteration = 0;
            $count = count($alegs);
            foreach ($alegs as $aleg) {
                $this->log(" day = $aimsday record  = $iteration  from  = $count ", "yellow");
                $iteration++;
                $flight=null;
                $isnewflight=false;
                $error = $this->saveFlight($aleg, $flight, $isnewflight);
                if (!$error && $flight) {
                    $isnewleg = '';
                    $errorleg = $this->saveFlightLeg($aleg, $flight->id, $isnewleg);
                    if (!$errorleg) {
                        if ($isnewleg) {
                            $count_create_leg++;
                        } else {
                            $count_update_leg++;
                        }
                    } else {
                        print_r($errorleg);
                        $count_error_leg++;
                    }
                    if ($isnewflight) {
                        $count_create_flight++;
                    } else {
                        $this->updateTimeFlight($flight->id);
                        $count_update_flight++;
                    }
                    // save crew for flight
                    $this->saveCrew($flight);
                    echo "*";
                } else {
                    $count_error_flight++;
                    print_r($error);
                    echo "-";
                }
            }

            $this->log(' --------Flight Legs ------------ ', "blue");
            $this->log('Create Leg ='.$count_create_leg, "green");
            $this->log('Update Leg ='.$count_update_leg, "green");
            $this->log('Error Leg ='.$count_error_leg, "red");
            $this->log(' --------Flights ------------ ', "blue");
            $this->log('Create Flight ='.$count_create_flight, "green");
            $this->log('Update Flight ='.$count_update_flight, "green");
            $this->log('Error Flight ='.$count_error_flight, "red");
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

    /**
     *  Save and Update Flight from $data
     * @param $data
     * @param $flight
     * @param $isnewflight
     * @return array
     */
    private function saveFlight($data, &$flight, &$isnewflight)
    {
        $output = [];
        $isnewflight = false;
        $flight = Flight::find()
            ->where([
                'day'              => $data['DAY'],
                'flt'              => $data['FLT'],
                'carrier'          => $data['CARRIER'],
            ])
            ->one();
        if (!$flight) {
            $isnewflight = true;
            $flight = new Flight();
            $depAirport = Airport::findOne(['iata'=>trim($data['DEP'])]);
            $arrAirport = Airport::findOne(['iata'=>trim($data['ARR'])]);
            $aircraft   = Aircraft::findOne(['reg'=>trim($data['REG'])]);

            if ($depAirport && $arrAirport && $aircraft) {
                $flight->day            =  $data['DAY'];
                $flight->flt            =  $data['FLT'];
                $flight->fltDes         =  $data['FLT']. $data['LEGCD'];
                $flight->os             =  $data['LEGCD'];
                $flight->carrier        =  $data['CARRIER'];
                $flight->aircraft_id    =  $aircraft->id;
                $flight->dep_airport_id =  $depAirport->id;
                $flight->arr_airport_id =  $arrAirport->id;
                $flight->std            =  $data['STD'];
                $flight->sta            =  $data['STA'];
                $flight->etd            =  $data['ETD'];
                $flight->eta            =  $data['ETA'];
                $flight->arr_gate       =  $data['ARR_GATE'];
                $flight->dep_gate       =  $data['DEP_GATE'];
                $flight->arr_stand      =  $data['ARR_STAND'];
                $flight->dep_stand      =  $data['DEP_STAND'];
                $flight->arr_weather    =  '[]';
                $flight->dep_weather    =  '[]';
                $flight->canceled       =  $data['CANCELLED'];

                if (!$flight->save()) {
                    $output[] = ['error' => $flight->errors];
                }
            } else {
                if (!$aircraft) {
                    $output[] = ['error' => 'not found Aircraft  reg='.trim($data['REG'])];
                }

                if (!$depAirport) {
                    $output[] = ['error' => 'not found Airport  reg='.trim($data['DEP'])];
                }

                if (!$arrAirport) {
                    $output[] = ['error' => 'not found Airport  reg='.trim($data['ARR'])];
                }
            }
        }
        return $output;
    }

   /**
    * Save and Update Flight Passenger from $data
    *
    * @param array $data
    */
   private function saveFlighPassenger($data)
   {
    // To do
   }

    /**
     * Update  sta, std, eta? etd for Flight
     * @param $flight_id
     * @return bool
     */
    private function updateTimeFlight($flight_id)
    {
        // find flight
        $flight = Flight::findOne($flight_id);
        // find  Legs for the Flight
        $legs = FlightLeg::find()
            ->where([
                   'flight_id' => $flight_id
            ])
            ->orderBy(['std'=>'ASC'])
            ->all();

        // update times
        $begin = reset($legs);
        $end   = reset($legs);

//        $flight->dep = $begin->dep;
//        $flight->arr = $end->arr;
//
//        $flight->dep_stand = $begin->dep_stand;
//        $flight->arr_stand = $end->arr_stand;
//
//        $flight->dep_gate = $begin->dep_gate;
//        $flight->arr_gate = $end->arr_gate;
//
//
//        $flight->std = $begin->std;
//        $flight->etd = $begin->etd;
//
//        $flight->sta = $end->sta;
//        $flight->eta = $end->eta;

        return $flight->save();

    }


    /**
     * Update Crew member for Flight
     * write message to console
     *
     * @param Flight $flight
     */
    private function saveCrew($flight)
    {

        $count_update   = 0;
        $count_create   = 0;
        $count_error    = 0;

        $leg = $flight->firstLeg;

        $where = [
            't.DAY'        =>  $leg->day,
            't.CARRIER'    =>  $leg->carrier,
            't.FLT'        =>  $leg->flt,
//         't.DEP'        =>  trim($leg->dep)    ?????????????????????????????????????????????????
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

            $isnew = $crew->isNewRecord;
            if ($crew->save()) {
                if ($isnew) {
                    $count_create++;
                } else {
                    $count_update++;
                }
            } else {
                $count_error++;
            }
        }

        $this->log(' --------Leg Crew -------- ', "blue");
        $this->log('Create Leg ='.$count_create, "green");
        $this->log('Update Leg ='.$count_update, "green");
        $this->log('Error Leg ='.$count_error, "red");
    }

    /**
     * Action truncate
     */
    public function actionTruncate()
    {
        $table = Flight::tableName();
        Yii::$app->db->createCommand()->truncateTable($table)->execute();
        $table = FlightLeg::tableName();
        Yii::$app->db->createCommand()->truncateTable($table)->execute();
        $table = FlightLegCrew::tableName();
        Yii::$app->db->createCommand()->truncateTable($table)->execute();
    }
}
