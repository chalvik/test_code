<?php

namespace common\modules\ccpFlight\controllers\api;

use common\modules\ccpFlight\models\Flight;
use common\modules\ccpFlight\models\FlightBriefings;
use common\modules\storagefiles\models\Storagefiles;
use api\rest\ActiveRestController;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

/**
 * Класс реализует методы для обработки апи запросов
 * для рейсов
 *
 * This is the api controller class for flights.
 * Class BriefingsController    /**
 * @package common\modules\ccpFlight\controllers\api
 * @property string $modelClass
 */
class BriefingsController extends ActiveRestController
{

    /**
     * @SWG\Get(
     *     path="/flight/briefings/view",
     *     tags={"briefings"},
     *     @SWG\Parameter(name="flight_id", in="query", required=true, type="integer"),
     *     @SWG\Response(
     *         response=201,
     *         description="Success response",
     *     ),
     *     security={{"Bearer": {}}}
     *)
     */

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
//        $parent = parent::behaviors();

        $parent['access'] = [
            'class' => AccessControl::className(),
            'except' => ['options','view', 'update'],
        ];
        return $parent;
    }

    public $modelClass = 'common\modules\ccpFlight\models\FlightBriefings';


    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['update']);
        unset($actions['view']);
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['delete']);
        
        return $actions;
    }

    /**
     * @param $flight_id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $flight_id = $id;
        $flight = Flight::findOne($flight_id);
        if ($flight) {
            if ($model = FlightBriefings::findOne(['flight_id' => $flight_id])) {

            } else {
                $model = new FlightBriefings(['flight_id' => $flight_id]);
                if (!$model->validate()) {
                    throw new NotFoundHttpException("Error create Briefings");
                }
            }
            $model->save();

        } else {
            throw new NotFoundHttpException("Flight number not found");
        }
        StorageFiles::xForceDownload($model->file_id);
    }
}
