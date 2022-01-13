<?php

namespace common\modules\ccpFlight\models\console;

use common\modules\ccpFlight\models\Flight;
use common\modules\ccpFlight\models\FlightRefusalFood;
use yii\helpers\Json;

/**
 * Class FlightParse
 * @package common\modules\ccpFlight\models\console
 */
class FlightRefusalFoodParse extends FlightRefusalFood
{

    /**
     * Обновляет или добавляет запись по остаткам пимтания на рейсе
     * @param bool $flight
     * @return bool
     */
    public static function UpdateForFlight(Flight $flight) : bool
    {
        $output = true;

       $data = self::Request($flight);
       foreach ($data as $item) {

           if ($item) {
               $model = self::find()
                   ->where([
                       'flight_id' => $flight->id,
                       'roster_id' => $item['crewMemberId']
                   ])
                   ->one();
               if (!$model) {
                   $model = new self();

                   $model->flight_id = $flight->id;
                   $model->roster_id = $item['crewMemberId'];
                }

               $model->crewMemberFullName = $item['crewMemberFullName'];
               $model->role = $item['role'];
               $model->status = $item['status'];
               $output = $model->save();
            }
       }

        return $output;
    }


    /**
     * Возвращает массив по отказам питания на рейсе
     * @param $flight
     * @return array|mixed
     */
    public static function Request($flight):array
    {
        $output = [];
        $url = \Yii::$app->params['RefusalFood'];

        $std = gmdate("Ymd", strtotime($flight->std. " UTC"));
        $dep = $flight->depAirport->iata;
        $flt = $flight->flt;

        // FLT=3661.DATE=20191211.DEP=DME.CARRIER=S7.OS=null
        $legid ="FLT=$flt.DATE=$std.DEP=$dep.CARRIER=S7.OS=null";
        $url = $url."/$legid";


        // Create a stream
        $options = [
            'http'=> [
                'method'=>"GET",
            ]
        ];

        $context = stream_context_create($options);
        $output = file_get_contents($url, false, $context);

        return ($output)?Json::decode($output):[];
    }

}
