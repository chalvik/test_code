<?php

namespace common\modules\scheduler\controllers\console;

use common\components\ArrayHelper;
use common\modules\ccpFlight\models\Flight;
use common\modules\EdbPassenger\models\EdbPassenger;
use common\modules\EdbPassenger\models\EdbPassengerCode;

use console\controllers\BaseController;
use yii\console\widgets\Table;
use yii\helpers\Json;

/**
 * Class InstallController for update and install task from scheduler
 * Class InstallController
 * @package common\modules\scheduler\controllers\console
 */
class TestController extends BaseController
{

    public function actionImportPassengers()
    {
        $passengers_json = json_decode(file_get_contents(\Yii::getAlias('@root') . "/log/passengers.json"),true);
       // print_r($passengers_json);
        foreach ($passengers_json as $passenger) {
            (new EdbPassenger($passenger))->save();
        }
    }


    public
    function actionDistinctPassenger($field, $from, $to)
    {

        $count_hours = $from * 60 * 60;
        $current_time = time();
        $start = gmdate("Y-m-d H:i", $current_time + $count_hours);
        $end = gmdate("Y-m-d H:i", $current_time + $count_hours + 3600 * $to);

        if ($passengers_fields = EdbPassenger::find()
            ->where(['between', 'STD_UTC', $start, $end])
            ->distinct()->select($field)->column()) {
            echo implode(PHP_EOL, $passengers_fields) . PHP_EOL;
        }

    }

    public
    function actionDistinctPassengerLocal($field)
    {


        if ($passengers_fields = EdbPassenger::find()
            ->distinct()->select($field)->column()) {
            echo implode(PHP_EOL, $passengers_fields) . PHP_EOL;
        }

    }

    public
    function actionPassengerBaseInfo($from, $to)
    {

        $count_hours = $from * 60 * 60;
        $current_time = time();
        $start = gmdate("Y-m-d H:i", $current_time + $count_hours);
        $end = gmdate("Y-m-d H:i", $current_time + $count_hours + 3600 * $to);

        $passengers = EdbPassenger::find()
            ->where(['between', 'STD_UTC', $start, $end])
            ->andWhere([
                'NOT IN', 'PASSENGER_STATUS', ['CANCEL', 'DELETED']
            ])->andWhere(['IS NOT', 'TICKET', null])
            ->limit(10)
            ->asArray()->all();

        if ($passengers) {
            // to Do
        }
        echo json_encode($passengers);
    }


    public
    function actionFreeUpgrade($from, $to)
    {

        $count_hours = $from * 60 * 60;
        $current_time = time();
        $start = gmdate("Y-m-d H:i", $current_time + $count_hours);
        $end = gmdate("Y-m-d H:i", $current_time + $count_hours + 3600 * $to);

        if ($passengers = EdbPassenger::find()
            ->where(['between', 'STD_UTC', $start, $end])
            ->andWhere([
                'NOT IN', 'PASSENGER_STATUS', ['CANCEL', 'DELETED']
            ])->andWhere(['IS NOT', 'TICKET', null])
            // ->groupBy('FLT')
            ->orderBy('STD_UTC')
            ->each(50)) {
            /** @var EdbPassenger $passenger */

            $flights = [];
            foreach ($passengers as $passenger) {
                $codes = [];
                if ($EMD_codes = (is_array($passenger->EMD)) ? $passenger->EMD : (Json::decode($passenger->EMD, true))) {
                    // filter by
                    $EMD_codes = array_filter($EMD_codes, function ($model) {
                        return ArrayHelper::getValue($model, 'rfisc') == '04D';
                    });
                }

                if ($passenger->CLASS_SERVICES == 'C' && !in_array($passenger->CLASS_BOOKING, ['C', 'D', 'U', 'J']) && !$EMD_codes) {
                    $codes[] = 'UPGR_FREE';
                    if ($flight = Flight::findOne(['flt' => $passenger->FLT, 'std' => $passenger->STD_UTC])) {
                        if ($pu = $flight->getPuCrew()->all()) {
                            $flights[$flight->flt] = "FLT=" . $flight->flt . " STD=" . $flight->std . " ROSTER=" . $pu[0]->roster_id;
                        };

                    }

                }


            }

            echo implode(PHP_EOL, $flights);
        }

    }


    public
    function actionSearchSpecialPassenger($from, $to, $flight_id = null)
    {

        $count_hours = $from * 60 * 60;
        $current_time = time();
        $start = gmdate("Y-m-d H:i", $current_time + $count_hours);
        $end = gmdate("Y-m-d H:i", $current_time + $count_hours + 3600 * $to);

        if ($flight_id) {
            $flights = Flight::find()->where(['id' => $flight_id])->all();
        } else {
            $flights = Flight::find()
                ->where(['between', 'std', $start, $end])
                ->limit(500)
                ->each(50);
        }

        //  return 1;

        if ($flights) {
            foreach ($flights as $flight) {
                echo ".";
                if ($passengers = $flight->passengers) {
                    foreach ($passengers as $passenger) {
                        $codes = EdbPassengerCode::codesPassenger($passenger);
                        // echo ".".implode(",", $codes).PHP_EOL;
                        if (array_intersect($codes, ['ID'])) {
                            echo PHP_EOL . " FLIGHT_ID=" . $flight->id . " FLT=" . $flight->flt . " STD=" . $flight->std . " ROSTER=" . $flight->   puCrew ? $flight->puCrew->roster_id : '';
                        }
                    }
                }
            }

        }

    }

    public
    function actionSearchSpecialPassengerAmadeus($flt, $std, $to = null)
    {
        if ($to) {
            $query = EdbPassenger::find()->where(['between', 'STD_UTC', $std, $to]);
        } else {
            $query = EdbPassenger::find()->where(['STD_UTC' => $std])->andWhere(['FLT' => $flt]);
        }

        $query->andWhere(['like', 'GROUP_NAME', 'EKIPAZH'])
            ->andWhere([
                'NOT IN', 'PASSENGER_STATUS', ['CANCEL', 'DELETED']
            ])->andWhere(['IS NOT', 'TICKET', null])
            ->orderBy('STD_UTC');

        $foundTable = [];

        if ($passengers = $query->all()) {

            foreach ($passengers as $passenger) {
                /** @var $passenger EdbPassenger */
                $foundTable[] = [
                    'NAME' => $passenger->NAME,
                    'SURNAME' => $passenger->SURNAME,
                    'FLT' => $passenger->FLT,
                    'STD' => $passenger->STD_UTC,
                    'PAX_TYPE' => $passenger->PAX_TYPE,
                    'ROSTER' => $passenger->TICKET,
                    'GROUP_NAME' => $passenger->GROUP_NAME
                ];
            }
        }


        if ($foundTable) {
            echo Table::widget([
                'headers' => array_keys($foundTable[0]),
                'rows' => $foundTable,
            ]);
        }


    }


}