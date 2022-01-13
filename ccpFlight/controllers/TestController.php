<?php
/**
 * Created by PhpStorm.
 * User: lexa
 * Email: achernogor@iseck.com
 * Date: 30.11.19
 * Time: 12:57
 */

namespace common\modules\ccpFlight\controllers;


use common\components\SSOLogin;
use common\modules\ccpFlight\models\Flight;
use yii\web\Controller;
use Yii;

class TestController extends Controller
{


    /**
     * Lists all FlightBriefings models.
     * @return mixed
     */
    public function actionFlightFeatures()
    {
        $url = Yii::$app->params['ssoFeatureFlightUrl'];

        $date = \Yii::$app->request->post('date');
        $flt = \Yii::$app->request->post('flt');
        $result = '';


        if (Yii::$app->request->isPost) {

            $data = [
                'flightNumber' => $flt,
                'actualDate' => strtotime($date) * 1000,
            ];

            $sso = new SSOLogin();
            $result = $sso->request($url, $data);

        }

        return $this->render('flight-features', [
            'request' => $result,
            'date' => $date,
            'flt' => $flt,
        ]);
    }


    /**
     * Lists all FlightBriefings models.
     * @return mixed
     */
    public function actionRefusalFood()
    {

        $url = Yii::$app->params['RefusalFood'];
        $date = \Yii::$app->request->post('date');
        $flt = \Yii::$app->request->post('flt');
        $flight_id = \Yii::$app->request->post('flt_id');
        $request = '';

        $params = [
            'url' => $url
        ];


        if (Yii::$app->request->isPost) {

            $flight = null;
            if ($flight_id) {
                $flight = Flight::find()->where(['id' => $flight_id])->one();
            } else {
                $flight = Flight::find()->where([
                    'std' => $date,
                    'flt' => $flt
                ]) ->one();
            }

            /** @var $flight Flight */
            if ($flight) {

                $std = gmdate("Ymd", strtotime($flight->std. " UTC"));
                $dep = $flight->depAirport->iata;
                $flt = $flight->flt;

                // FLT=3661.DATE=20191211.DEP=DME.CARRIER=S7.OS=null
                $legid ="FLT=$flt.DATE=$std.DEP=$dep.CARRIER=S7.OS=null";
                $url = $url."/$legid";

                $data = [
                    'FLT' => $flt,
                    'DEP' => $dep,
                    'STD' => $std,
                ];

                $params['data'] = $data;
                $params['legid'] = $legid;
                $params['url'] = $url;


                // Create a stream
                $options = [
                    'http'=> [
                        'method'=>"GET",
                        'content' => "",
                    ]
                ];

                $context = stream_context_create($options);
                $request = file_get_contents($url, false, $context);

            } else {
                $request = "Не корректно указаны данные по рейсу";
            }

        }

        return $this->render('refusal-food', [
            'params' => $params,
            'request' => json_decode($request),
            'date' => $date,
            'flt' => $flt,
            'flt_id' => $flight_id,
        ]);
    }


}