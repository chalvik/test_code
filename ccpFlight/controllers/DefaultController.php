<?php

namespace common\modules\ccpFlight\controllers;

use common\modules\ccpFlight\models\CcpFlightForceUpdate;
use common\modules\ccpFlight\models\Flight;
use common\modules\ccpFlight\models\search\FlightSearch;
use common\modules\ccpFlight\tasks\TaskFlightCrewForceUpdate;
use common\modules\report\tasks\TaskCreateReportExcel;
use common\modules\scheduler\models\SchedulerTask;
use Yii;
use common\components\BaseController;
use yii\helpers\Url;

/**
 * Контроллер для админки разработчика (backend)
 * реализует методы для работы, с рейсами
 *
 * FlightController implements the CRUD actions for Flight model.
 * Class DefaultController
 * @package common\modules\ccpFlight\controllers
 * @property string $modelName
 */
class DefaultController extends BaseController
{
    public $modelName = 'Flight';


    public function actions()
    {
        $actions = parent::actions();

        $actions['doc'] = [
            'class' => 'light\swagger\SwaggerAction',
            'restUrl' => Url::to(['/admin/flight/default/api'], true),
        ];
        $actions['api'] = [
            'class' => 'light\swagger\SwaggerApiAction',
            //The scan directories, you should use real path there.
            'scanDir' => [
                Yii::getAlias('@root') . '/common/modules/ccpFlight/controllers/api',
            ]
        ];

        return $actions;

    }

    public function actionSmartSearch()
    {
        $searchModel = new FlightSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $dataProvider->totalCount;
    }

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    /**
     * @inheritdoc
     */
    public function actionForceUpdate()
    {
        if ($flight_id = Yii::$app->request->post('flight_id')) {
            $FlightToForceUpdate = CcpFlightForceUpdate::findOne(['flight_id' => $flight_id]) ?: new CcpFlightForceUpdate(['flight_id' => $flight_id]);
            $FlightToForceUpdate->is_updated = false;
            $FlightToForceUpdate->save();

            // add task in line
            if ($task = SchedulerTask::find()->andWhere(['like', 'class', TaskFlightCrewForceUpdate::className()])->one()) {
                $task->addInLine();
            }
        }

    }

    /**
     * @inheritdoc
     */
    public function actionView($id)
    {
        $result = [];
        $model = Flight::find()->with(
            [
                'arrAirport',
                'depAirport',
                'crew',
                'loadDetails',
                'loadCompartment',
                'ffm',
                'mails',
                'aircraft',
                'notifications',
                'chatTickets'
            ]
        )->where(['id' => $id])->one();


        $time = (strtotime($model->sta) - strtotime($model->blof));
        $current = (strtotime($model->sta) - time()) * 100 / $time;
        $current = 100 - $current;

        $coordinates['lat'] = $model->arrAirport->latitude;
        $coordinates['lon'] = $model->arrAirport->longitude;
        $coordinates['label'] = $model->arrAirport->city;
        $coordinates['iata'] = $model->arrAirport->iata;
        $result[] = $coordinates;

        $coordinates['lat'] = $model->depAirport->latitude;
        $coordinates['lon'] = $model->depAirport->longitude;
        $coordinates['label'] = $model->depAirport->city;
        $coordinates['iata'] = $model->depAirport->iata;
        $result[] = $coordinates;

        return $this->render(
            'view',
            [
                'model' => $this->findModel($id),
                'coords' => $result,
                'current' => $current,
            ]
        );
    }

    /**
     * ВЫводит краткую инфрормацию о рейсе, для аякс запроса в Grid
     * @return string
     */
    public function actionGridView()
    {
        $id = Yii::$app->request->post('expandRowKey');
        $model = $this->findModel($id);
        return Yii::$app->controller->renderPartial('_view/_grid_view', ['model' => $model]);
    }
}
