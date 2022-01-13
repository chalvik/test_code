<?php
namespace common\modules\ccpFlight\models\console;

use common\modules\ccpAircraft\models\Aircraft;
use common\modules\ccpAirport\models\Airport;
use common\modules\ccpFlight\exceptions\FlightException;
use common\modules\ccpFlight\models\Carrier;
use common\modules\ccpFlight\models\Flight;
use common\modules\ccpFlight\models\FlightLeg;
use common\modules\LogRefresh\models\LogRefresh;
use Yii;
use yii\db\Query;

/**
 * Class FlightLegParse
 * @package common\modules\ccpFlight\models\console
 */
class FlightLegParse extends FlightLeg
{

    /**
     * Save and Update Leg from $data
     *
     * @param array $data
     * @param Flight $flight
     * @param $isnewleg
     * @return bool
     * @throws FlightException
     */
    public static function saveFlightLeg($data, Flight $flight, &$isnewleg)
    {
        // tO DO Добавить вывод информации если не хватало данных
        $dep = trim($data['DEP']);
        $arr = trim($data['ARR']);
        $reg = trim($data['REG']);

        $depAirport = Airport::findOne(['iata' => $dep]);
        $arrAirport = Airport::findOne(['iata' => $arr]);
        $aircraft = Aircraft::findOne(['reg' => $reg]);


        $carrier_id = trim($data['CARRIER']);
        if (isset(Carrier::$carrier_list_aims[$carrier_id])) {
            $carrier = Carrier::$carrier_list_aims[$carrier_id];
        } else {
            $carrier = 'NONE';
        }

        $isnewleg = false;

        $model = FlightLeg::find()->where(
            [
                'day' => $data['DAY'],
                'flt' => $data['FLT'],
                'dep' => $dep,
                'carrier' => $data['CARRIER'],
                'legcd' => trim($data['LEGCD']),
            ]
        )->one();

        if (!$model) {
            $model = new FlightLeg();
            $isnewleg = true;
        }

        $model->day = $data['DAY'];
        $model->flight_id = $flight->id;
        $model->flt = $data['FLT'];
        $model->dep = trim($data['DEP']);
        $model->carrier = trim($data['CARRIER']);
        $model->legcd = trim($data['LEGCD']);
        $model->arr = $arr;
        $model->ac = $data['AC'];
        $model->reg = $reg;
        $model->canceled = $data['CANCELLED'];
        $model->adate = $data['ADATE'];
        $model->aroute = $data['AROUTE'];
        $model->std = $data['STD'];
        $model->sta = $data['STA'];
        $model->etd = (($data['ETD']) ?: null);
        $model->eta = (($data['ETA']) ?: null);
        $model->blof = $data['BLOF'];
        $model->tkof = $data['TKOF'];
        $model->tdown = $data['TDOWN'];
        $model->blon = $data['BLON'];

        $model->aircraft_id = (isset($aircraft)?$aircraft->id:0);
        $model->dep_airport_id = (isset($depAirport)?$depAirport->id:0);
        $model->arr_airport_id = (isset($arrAirport)?$arrAirport->id:0);

        $model->dep_gate = $data['DEP_GATE'];
        $model->arr_gate = $data['ARR_GATE'];
        $model->arr_stand = $data['ARR_STAND'];
        $model->dep_stand = $data['DEP_STAND'];

        $model->last_updated_at = gmdate("Y-m-d H:i:s");
        
        if ($model->isNewRecord) {
            $model->last_load_updated_at = gmdate("Y-m-d H:i:s", strtotime('-20 minutes'));
            $model->last_restriction_updated_at = gmdate("Y-m-d H:i:s", strtotime('-20 minutes'));
        }

        return $model->save();
    }

    /**
     * Get Flight Legs array data records for $where  from Oracle (Aims)
     *
     * @param array $where
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public static function getLegsFromOracle($where = [], $limit = 500, $offset = 0)
    {
        $time = time();

        $query = (new Query())
            ->select([
                'DAY',
                'FLT',
                'DEP',
                'CARRIER',
                'LEGCD',
                'ARR',
                'AC',
                'REG',
                'CANCELLED',
                'ADATE',
                'AROUTE',
                'STD'               => "to_char(STD_DATE, 'yyyy-mm-dd hh24:mi:ss')",
                'STA'               => "to_char(STA_DATE, 'yyyy-mm-dd hh24:mi:ss')",
                'ETD'               => "to_char(ETD_DATE, 'yyyy-mm-dd hh24:mi:ss')",
                'ETA'               => "to_char(ETA_DATE, 'yyyy-mm-dd hh24:mi:ss')",
                'BLOF'              => "to_char(BLOF_DATE, 'yyyy-mm-dd hh24:mi:ss')",
                'TKOF'              => "to_char(TKOF_DATE, 'yyyy-mm-dd hh24:mi:ss')",
                'TDOWN'             => "to_char(TDOWN_DATE, 'yyyy-mm-dd hh24:mi:ss')",
                'BLON'              => "to_char(BLON_DATE, 'yyyy-mm-dd hh24:mi:ss')",
                'ORIGINAL_STD_DATE' => "to_char(ORIGINAL_STD_DATE, 'yyyy-mm-dd hh24:mi:ss')",
                'DEP_GATE',
                'ARR_GATE',
                'DEP_STAND',
                'ARR_STAND',
            ])
            ->from(['t' => 'S7_IT.CCP#V_LEGS'])
            ->where($where)
            ->orderBy('STD ASC');

        if ($limit) {
            $query = $query->limit($limit);
        }
        if ($offset) {
            $query = $query->offset($offset);
        }
        $legs = $query->all(Yii::$app->dbAmis);

        $time = time() - $time;
        LogRefresh::add(
            LogRefresh::PARENT_FLIGHT_LEGS,
            'response count legs :'.count($legs),
            json_encode($where)." limit:$limit offset:$offset",
            '',
            $time,
            0
        );
        
        return $legs;
    }

    /**
     * Get Flight Legs count records for $where  from Oracle (Aims)
     *
     * @param array $where
     * @return int|string
     */
    public static function getCountLegsFromOracle($where = [])
    {
        return (new \yii\db\Query())
            ->from(['t' => 'S7_IT.CCP#V_LEGS'])
            ->where($where)
            ->count('*', \Yii::$app->dbAmis);
    }


    /**
     * Возвращает массив для формирование запроса поиска рейсов во внешней Oracle базе,
     * вхожящие данные это рейсы, или все рейсы и дата
     * дата обязательно , рейс если 0, то выбрать все рейсы
     * поле flight может иметь формат, обязательно к заполнению
     *      flt - номер рейса
     *      all - все рейсы
     * поле date формат ввода часовой пояс UTC
     * -  date:Y-m-d
     * -  timestamp:1412609982
     *
     * Get array for where to ActiveQuery
     * @param $flt
     * @param $date
     * @return array
     */
    public static function getWhereFromFlightLegOracle($flt, $date)
    {
        $where = [];

        if (! in_array($date, self::$types_date) && strpos($date, ':')) {
            $array = explode(':', $date);
            $type = $array[0];
            $date = $array[0];
            if (in_array($type, self::$all_flights)) {
                if ($type === 'timestamp') {
                    $start = gmdate("Y-m-d H:i", $tstart);
                    $end = gmdate("Y-m-d H:i", $tstart+$count_day);
                    $where = "STD_DATE BETWEEN TO_DATE('$start', 'yyyy-mm-dd hh24:mi') AND TO_DATE ('$end', 'yyyy-mm-dd hh24:mi')" ;
                } elseif ($type === 'date' and preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}/', $date)) {
                    $date = strtotime($date.' 00:00:00 UTC');
                    $start = gmdate("Y-m-d H:i", $tstart);
                    $end = gmdate("Y-m-d H:i", $tstart+$count_day);
                    $where = "STD_DATE BETWEEN TO_DATE('$start', 'yyyy-mm-dd hh24:mi') AND TO_DATE ('$end', 'yyyy-mm-dd hh24:mi')" ;
                }
            }
        }
        return $where;
    }
}
