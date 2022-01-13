<?php

namespace common\modules\ccpFlight\controllers\api;

use api\rest\ActiveRestController;
use backend\models\D;
use common\modules\ccpFlight\models\search\FeatureFlightSearch;
use common\modules\ccpUser\models\User;
use common\modules\food\models\search\MealTypeSearch;
use Yii;
use yii\filters\AccessControl;

/**
 * Класс реализует методы для обработки апи запросов
 * для справочника типа блюд
 *
 * Class MealTypeController
 * @package common\modules\food\controllers\api
 * @property string $modelClass
 */
class FeaturesController extends ActiveRestController
{
    public $modelClass = 'common\modules\ccpFlight\models\FeatureFlight';

    /**
     * @SWG\Get(
     *     path="/flight/features",
     *     tags={"flight/features"},
     *     description="start_date,end_date in datetime format 2019-12-03 12:45:00",
     *     @SWG\Parameter(name="query", in="query", required=false, type="string"),
     *     @SWG\Parameter(name="flight_flt", in="query", required=false, type="integer"),
     *     @SWG\Parameter(name="start_date", in="query", required=false, type="string"),
     *     @SWG\Parameter(name="end_date", in="query", required=false, type="string"),
     *     @SWG\Response(
     *         response=200,
     *         description="Success response",
     *     ),
     *     security={{"Bearer": {}}}
     * )
     *
     * @SWG\Get(
     *     path="/flight/features/{id}",
     *     tags={"flight/features"},
     *     description="",
     *     @SWG\Parameter(name="id", in="path", required=true, type="integer"),
     *     @SWG\Response(
     *         response=200,
     *         description="Success response",
     *     ),
     *     security={{"Bearer": {}}}
     * )
     *
     * @SWG\Post(
     *     path="/flight/features/create",
     *     tags={"flight/features"},
     *     description="start_date,end_date in datetime format 2019-12-03 12:45:00",
     *     @SWG\Parameter(name="flight_flt", in="formData", required=true, type="integer"),
     *     @SWG\Parameter(name="note", in="formData", required=true, type="string"),
     *     @SWG\Parameter(name="start_date", in="formData", required=true, type="string"),
     *     @SWG\Parameter(name="end_date", in="formData", required=true, type="string"),
     *     @SWG\Response(
     *         response=201,
     *         description="Success response",
     *     ),
     *     security={{"Bearer": {}}}
     *)
     *
     *
     * @SWG\Post(
     *     path="/flight/features/delete/{id}",
     *     tags={"flight/features"},
     *     @SWG\Parameter(name="id", in="path", required=true, type="integer"),
     *     @SWG\Response(
     *         response=200,
     *         description="Success response",
     *     ),
     *     security={{"Bearer": {}}}
     *)
     *
     * @SWG\Put(
     *     path="/flight/features/update/{id}",
     *     tags={"flight/features"},
     *     description="start_date,end_date in datetime format 2019-12-03 12:45:00",
     *     @SWG\Parameter(name="id", in="path", required=true, type="integer"),
     *     @SWG\Parameter(name="flight_flt", in="formData", required=true, type="integer"),
     *     @SWG\Parameter(name="note", in="formData", required=true, type="string"),
     *     @SWG\Parameter(name="start_date", in="formData", required=true, type="string"),
     *     @SWG\Parameter(name="end_date", in="formData", required=true, type="string"),
     *     @SWG\Response(
     *         response=201,
     *         description="Success response",
     *     ),
     *     security={{"Bearer": {}}}
     *)
     *
     */

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $parent = parent::behaviors();
        $parent['access'] = [
            'class' => AccessControl::className(),
            'except' => ['options'],
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['admin', User::INTRA_ROLE, User::PERMISSION_FLIGHT_FEATURES],
                ],
                [
                    'allow' => true,
                    'matchCallback' => function () {
                        return Yii::$app->user->identity instanceof \common\models\User/* && Yii::$app->user->identity->isAdmin()*/
                            ;
                    }
                ],
            ],
        ];
        return $parent;
    }


    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    /**
     * Предварительная обработка данных перед отправкой массива блюд
     * @return object
     * @throws \yii\base\InvalidConfigException
     */

    public function prepareDataProvider()
    {

        $searchModel = new FeatureFlightSearch();
       return $searchModel->search(Yii::$app->request->queryParams);

    }


}
