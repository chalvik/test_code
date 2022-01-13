<?php

namespace common\modules\ccpFlight\controllers\api;

use common\modules\ccpFlight\models\FlightLegCrew;
use common\modules\ccpUser\models\User;
use Yii;
use api\rest\ActiveRestController;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * Класс реализует методы для обработки апи запросов
 * для экипажа рейсов
 *
 * This is the api controller class for   cre leg of flight.
 * Class LegCrewController
 * @package common\modules\ccpFlight\controllers\api
 */
class LegCrewController extends ActiveRestController
{

    public $modelClass = 'common\modules\ccpFlight\models\FlightLegCrew';

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        $verbs = parent::verbs();
        $verbs['roster-id'] = ['GET', 'HEAD'];
        return $verbs;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();
        // customize the data provider preparation with the "prepareDataProvider()" method
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        unset($actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    /**
     * @inheritdoc
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function prepareDataProvider()
    {
        $flight_flt = Yii::$app->request->get('flight_flt');
        $flight_id = Yii::$app->request->get('flight_id');
        $date = Yii::$app->request->get('date');

        if (!$flight_id and !$flight_flt) {
            throw new NotFoundHttpException("Page not found");
        }

        $query = FlightLegCrew::find();

        if ($flight_id) {
            $query->andWhere(['flight_id' => $flight_id]);
        }
        $query->joinWith('flight');

        if ($date) {
            $query->andWhere(['<=', 'ccp_flight.std', gmdate("Y-m-d 23:59:59", strtotime($date . ' UTC'))]);
            $query->andWhere(['>=', 'ccp_flight.std', gmdate("Y-m-d 00:00:00", strtotime($date . ' UTC'))]);
        }
        if ($flight_flt) {
            $query->andWhere(['=', 'ccp_flight.flt', $flight_flt]);
        }

        return Yii::createObject([
            'class' => ActiveDataProvider::className(),
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC,
                ]
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
    }

    /**
     * возвращает объект члена экипажа по табельному номеру
     * Get Crew for roster_id
     * @param $roster_id
     * @return mixed
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionRosterId($roster_id)
    {
        /* @var $modelClass FlightLegCrew */
        $modelClass = $this->modelClass;
        $model = $modelClass::findOne(['roster_id' => $roster_id]);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException("Page not found");
        }
        return $model;
    }

    /**
     * Метод обновления записи pos_pu
     * @param $id int ccp_flight_leg_crew таблица поле id
     * @return FlightLegCrew
     * @throws ServerErrorHttpException
     * @throws NotFoundHttpException
     * @throws UnauthorizedHttpException
     */
    public function actionUpdatePosPu($id)
    {
        /* @var $modelClass FlightLegCrew */
        $modelClass = $this->modelClass;
        /* @var $model FlightLegCrew */
        $model = $modelClass::findOne($id);
      if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
      }
      /** @var User $user */
        $user = Yii::$app->user->identity;
        if (!$user->isSuperUser) {
            $crewPu = FlightLegCrew::find()
                ->where([
                    'roster_id' => $user->roster_id,
                    'pos_code' => 'PU',
                    // Only PU of the same flight can change positions, comment to disable
                    'flight_id' => $model->flight_id
                ])->one();
            if (!$crewPu) {
                throw new NotFoundHttpException("Only PU can change position or flight not found");
            }
        }

        $posPu = Yii::$app->request->getBodyParam('pos_pu');
        if (!$posPu) {
            throw new NotFoundHttpException("Variable pos_pu is not set");
        }
        $model->pos_pu = $posPu;
        
        $posFlight = Yii::$app->request->getBodyParam('pos_flight');
        $model->pos_flight = $posFlight ?: $posPu;

        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        return $model;
    }
}