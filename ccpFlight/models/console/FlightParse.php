<?php

namespace common\modules\ccpFlight\models\console;

use common\modules\ccpFlight\models\Flight;
use common\modules\ccpFlight\exceptions\FlightException;
use common\modules\ccpAirport\models\Airport;
use common\modules\ccpAircraft\models\Aircraft;
use common\modules\ccpFlight\models\Carrier;
use common\modules\LogRefresh\models\LogRefresh;
use common\modules\scheduler\models\ExtendedLogger;

/**
 * Class FlightParse
 * @package common\modules\ccpFlight\models\console
 */
class FlightParse extends Flight
{

    /**
     * Save and Update Flight from $data
     *
     * @param array $data flight
     * @param boolean $isnew Flag
     * @return \common\modules\ccpFlight\models\console\FlightParse
     * @throws FlightException
     */
    public static function saveFlight($data)
    {

        $dep = trim($data['DEP']);
        $arr = trim($data['ARR']);
        $reg = trim($data['REG']);

        $depAirport = Airport::findOne(['iata' => $dep]);
        $arrAirport = Airport::findOne(['iata' => $arr]);


        $aircraft = Aircraft::find()
            ->where([
                'reg' => $reg,
                'deleted' => false
            ])
            ->one();

        if (!$depAirport || !$arrAirport || !$aircraft || !$data['FLT']) {
            return false;
        }

        $query = self::find()->where(
            [
                //'day' => $data['DAY'],
                'flt' => $data['FLT'],
                'std' => $data['STD'],
                'dep_airport_id' => $depAirport->id,
                'carrier' => $data['CARRIER'],
            ]
        );

        if (isset($data['LEGCD'])) {
            $query = $query->andWhere(['os' => $data['LEGCD']]);
        } else {
            $query = $query->andWhere(['IS NOT', 'os', NULL]);
        }

        $model = $query->one();

        if (!$model) {
            $model = new self();
        }

        $carrier_id = trim($data['CARRIER']);
        if (isset(Carrier::$carrier_list_aims[$carrier_id])) {
            $carrier = Carrier::$carrier_list_aims[$carrier_id];
        } else {
            $carrier = 'NONE';
        }

        $model->day = $data['DAY'];
        $model->flt = $data['FLT'];
        $model->fltDes = $carrier . ' ' . $data['FLT'] . ' ' . $data['LEGCD'];
        $model->os = ($data['LEGCD']) ?: null;
        $model->carrier = $carrier_id;
        $model->aircraft_id = (isset($aircraft) ? $aircraft->id : 0);
        $model->dep_airport_id = (isset($depAirport) ? $depAirport->id : 0);
        $model->arr_airport_id = (isset($arrAirport) ? $arrAirport->id : 0);



        $model->origin_std_date = $data['ORIGINAL_STD_DATE'];
        $model->std = $data['STD'];
        $model->sta = $data['STA'];
        $model->etd = (isset($data['ETD']) ? $data['ETD'] : null);
        $model->eta = (isset($data['ETA']) ? $data['ETA'] : null);

        $model->blon = $data['BLON'];
        $model->blof = $data['BLOF'];
        $model->tkof = $data['TKOF'];
        $model->tdown = $data['TDOWN'];

        $model->estimated = self::CalcEtimated($data['BLOF'], $data['STD']);

        $model->arr_gate = (string) $data['ARR_GATE'];
        $model->dep_gate = (string) $data['DEP_GATE'];
        $model->arr_stand = (string) $data['ARR_STAND'];
        $model->dep_stand = (string) $data['DEP_STAND'];
        $model->arr_weather = '[]';
        $model->dep_weather = '[]';
        $model->canceled = $data['CANCELLED'];
        $model->last_updated_at = gmdate("Y-m-d H:i:s");

        if (!$model->eta) {
           // ExtendedLogger::storeLog(" ETD FOR FLIGHT_ID=".$model->id." FLT=".$model->flt." STD ".$model->std."IS NOT ISSET ");
        }

        if (!$model->aircraft_id) {
            ExtendedLogger::storeLog(" AIRCRAFT FOR FLIGHT_ID=".$model->id." FLT=".$model->flt." STD ".$model->std."IS NOT ISSET ");
        }
        
        if (!$model->save()) {
            throw new FlightException(json_encode($model->errors));
        }

        return $model;
    }

    /**
     * Update Crew in flight
     *
     * @return bool
     */
    public function updateCrew()
    {
        $status = true;
        $count = [];
        $count['new'] = 0;
        $count['update'] = 0;
        $count['delete'] = 0;

        $where = [
            'std' => $this->std,
            't.CARRIER' => $this->carrier,
            't.FLT' => $this->flt,
            't.LEGCD' => $this->os
        ];

        $time = microtime(true);
        $acrews = FlightCrewParse::getCrewFromOracle($where);
        $crews_id = [];

        LogRefresh::add(
            LogRefresh::PARENT_FLIGHT_CREW,
            json_encode($where),
            json_encode($acrews),
            '',
            $time - microtime(true),
            0,
            $this->id
        );

        foreach ($acrews as $key => $acrew) {
            if (!$acrew['ID_DHD']) {
                $crews_id[] = trim($acrew['ROSTER_ID']);
            } else {
                unset($acrews[$key]);
            }
        }

        $flCrews = FlightCrewParse::find()
            ->where(['flight_id' => $this->id])
            ->andWhere(
                [
                    'NOT IN',
                    'roster_id',
                    $crews_id
                ]
            )
            ->all();

        foreach ($flCrews as $crew) {
            if ($crew->delete()) {
                $count['delete']++;
            }
        }

        $transaction = FlightCrewParse::getDb()->beginTransaction();
        try {
            foreach ($acrews as $crew) {
                $result = FlightCrewParse::saveCrew($this, $crew, $is_new);
                if ($result && $is_new) {
                    $count['new']++;
                } elseif ($result) {
                    $count['update']++;
                }
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            $count['new'] = 0;
            $count['update'] = 0;
            $status = false;
        }

        return $status;
    }

    /**
     * Calculate Estimated time
     *
     * @param string $dblof Timestamp field DBlof
     * @param string $dsta Timestamp field STD
     * @return false|string
     */
    public static function calcEtimated($dblof, $dstd)
    {
        $period = 50 * 60; // 1 hour;

        if ((time() > strtotime($dstd . ' UTC')) && (!$dblof)) {
            $estimated = gmdate("Y-m-d H:i:s", (time() + $period));
        } elseif (time() < strtotime($dstd . ' UTC') && !$dblof) {
            $estimated = gmdate("Y-m-d H:i:s", strtotime($dstd . ' UTC') + $period);
        } else {
            $estimated = $dblof;
        }

        return $estimated;
    }

    /**
     * Return  array flithts for update
     *
     * @param integer $after Time second $after
     * @param integer $before Before time second
     * @return array
     */

    public static function getFlightsForUpdating($after = 1 * 60 * 60, $before = 2 * 60 * 60)
    {
        $current_second = time();
        $after = gmdate("Y-m-d H:i:s", $current_second - $after);
        $before = gmdate("Y-m-d H:i:s", $current_second + $before);
        $start = gmdate("Y-m-d H:i:s", $current_second - 10 * 60);
        $end = gmdate("Y-m-d H:i:s", $current_second + 10 * 60);

        $query = self::find()
            ->where(['between', 'estimated', $after, $before])
            //      ->andWhere(['canceled',0])git
            ->andWhere(['>', 'eta', gmdate("Y-m-d H:i:s", $current_second - 30 * 60)])
            ->orWhere(['between', 'std', $after, $before])
            ->orWhere(['between', 'blon', $start, $end])
            ->orderBy('std DESC');


        return $query->all();
    }

    /**
     * Возвращает массив для формирование запроса поиска рейсов,
     * вхожящие данные это рейсы, или все рейсы и дата
     * дата обязательно , рейс если 0, то выбрать все рейсы
     * поле flight может иметь формат, обязательно к заполнению
     *      flt_<number> - номер рейса
     *      id_<number> - номер рейса
     *      all - все рейсы
     * поле date формат ввода часовой пояс UTC
     * -  date:Y-m-d
     * -  timestamp:1412609982
     *
     * Get array for where to ActiveQuery
     * @param $flight
     * @param $date
     * @return array
     */
    public static function getWhereFromFlight($flight, $params)
    {
//        foreach ($params as $key => $value) {
//         // To do
//        }

        $where = [];
        if (!in_array($flight, self::$all_flights) && strpos($flight, ':')) {
            $array = explode(':', $flight);
            $type = $array[0];
            if (in_array($type, self::$all_flights)) {
                if ($type === 'flt') {
                    $where['flt'] = $array[1];
                } elseif ($type === 'id') {
                    $where['id'] = $array[1];
                }
            }
        }

        return $where;
    }

}
