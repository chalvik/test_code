<?php

namespace common\modules\ccpFlight\controllers\api;

use common\modules\ccpFlight\models\Flight;
use common\modules\ccpFlight\models\FlightLegCrew;
use common\modules\ccpUser\models\helpers\VersionsControlHandler;
use common\modules\ccpUser\models\User;
use console\models\Aims;
use Yii;
use api\rest\ActiveRestController;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

/**
 * Класс реализует методы для обработки апи запросов
 * для рейсов
 *
 * This is the api controller class for flights.
 * Class DefaultController    /**
 * @package common\modules\ccpFlight\controllers\api
 * @property string $modelClass
 */

/**
 * @SWG\Swagger(
 *     basePath="/",
 *     host=API_HOST,
 *     schemes={HOST},
 *     produces={"application/json","application/xml"},
 *     consumes={"application/json","application/xml","application/x-www-form-urlencoded"},
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="S7 CCP API",
 *         description="HTTPS JSON API",
 *     ),
 *     @SWG\SecurityScheme(
 *         securityDefinition="Bearer",
 *         type="apiKey",
 *         name="Authorization",
 *         in="header"
 *     )
 * )
 */
class DefaultController extends ActiveRestController
{

    public $modelClass = 'common\modules\ccpFlight\models\Flight';

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
                    'roles' => ['@'],
                ],
            ],
        ];
        return $parent;
    }

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        $verbs = parent::verbs();
        $verbs['airports'] = ['GET', 'HEAD'];
        $verbs['numbers'] = ['POST', 'HEAD'];
        $verbs['filter-by-menu'] = ['POST', 'HEAD'];
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
        unset($actions['view']);
        return $actions;
    }

    /**
     * @inheritdoc
     * @throws NotFoundHttpException
     */
    public function prepareDataProvider()
    {
        $from = Yii::$app->request->get('from');
        $to = Yii::$app->request->get('to');
        $date = Yii::$app->request->get('date');
        /** @var User $user */
        $user = Yii::$app->user->identity;
//        $user->stewardRosterId;
        /* @var $modelClass Flight */
        $modelClass = $this->modelClass;
        $query = $modelClass::find();


        // здесь происхожит подключение связей, чтобы подтянуть это одним запросом
        $relationWidth = [];
        if (Yii::$app->request->get('expand')) {
            if ($expands = explode(",", Yii::$app->request->get('expand'))) {
                foreach ($expands as $expand) {
                    $relations = Flight::availableExtraRelations();
                    if (in_array($expand, $relations)) {
                        $relationWidth[] = $expand;
                    }
                }

            }
        }
        $query->with($relationWidth);


        // возвращаем только не отмененные рейсы
        $query->andWhere(['canceled' => 0]);

        // Если это супер пользователь
        if ($user->isSuperUser || ($user->isSuperSteward && $date)) {
            // фильтр про наличие PU на рейсе
            $subQuery = FlightLegCrew::find()
                ->where('ccp_flight_leg_crew.flight_id = ccp_flight.id')
                ->andWhere(['ccp_flight_leg_crew.pos_code' => 'PU']);
            $query->andWhere(['exists', $subQuery]);

            // если выбрана дата по которой нужно Super User у отправить список рейсов
            if ($date) {
                $date = gmdate("Y-m-d H:i:s", strtotime($date . ' UTC'));
                $aims_day = Aims::DateToAimsDate($date);
                $query = $query->andWhere(['ccp_flight.day' => $aims_day]);
            } else {
                throw new NotFoundHttpException('Failed parameter date = do not null');
            }

            // Если простой пользователь IOS
        } else {
            $user_cid = $user->roster_id;
            // фильтруем по рейсам за которыми закреплен пользовать
            $query = $query->joinWith(['crew']);
            $query = $query->andWhere(["ccp_flight_leg_crew.roster_id" => $user_cid]);
            if ($from) {
                $from = gmdate("Y-m-d H:i:s", strtotime($from.' 00:00:00'.' UTC'));
                $query = $query->andWhere([">=", 'ccp_flight.std', $from]);
            }
            if ($to) {
                $to = gmdate("Y-m-d H:i:s", strtotime($to.' 23:59:59'.' UTC'));
                $query = $query->andWhere(["<=", 'ccp_flight.std', $to]);
            }
        }


        if ($user && $query) {
            if ($firstFlightQuery = (clone $query)->one()) {
                // version control handler
                $appVersionsControl = new VersionsControlHandler(Yii::$app->user->id, Yii::$app->request->userAgent, $firstFlightQuery->id);
                $appVersionsControl->handle();
            }
        }


        return Yii::createObject(
            [
                'class' => ActiveDataProvider::className(),
                'query' => $query,
                'sort' => [
                    'defaultOrder' => [
                        'std' => SORT_ASC,
                    ],
                ],

                'pagination' => [
                    'pageSize' => 1000,
                ],
            ]
        );
    }
    
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $model->getAvailableMenu();
        
        return $model;
    }
    
    protected function findModel($id)
    {
        $model = $this->modelClass;
        /** @var $model Flight*/
        if (($model = $model::findOne($id)) !== null) {
            return $model;
        }
        
        throw new NotFoundHttpException('The requested model does not exist.');
    }

    /**
     * Возвращает ассоциативный массив номеров рейсов (flt) и ай ди
     * по дате вылета
     * @return   array
     * @throws NotFoundHttpException
     * @internal param int $carrier Номер компании
     * @internal param array $directions [[dep_aiport_id,arr_aiport_id],....[dep_arr,arr_n]]
     */

    public function actionDateNumbers() // проверить запрос к бд
    {
        $data = Yii::$app->getRequest()->getBodyParams();
        $date = (isset($data['date']) ? $data['date'] : null);

        if ($date) {
            $dayAims = Aims::DateToAimsDate($date);
            return Flight::find()
      //          ->select(['id', 'flt'])    // to Do
                ->where([
                    'day' => $dayAims
                ])
                ->all();
        } else {
            throw new NotFoundHttpException("Error post parameter");
        }
    }

    /**
     * Возвращает массив номеров рейсов (flt) по направлению и компании
     * направление это массив ай ди аэропортов отправления и прибытия
     * @return   array
     * @internal param array $directions [[dep_aiport_id,arr_aiport_id],....[dep_arr,arr_n]]
     * @internal param int $carrier Номер компании
     */
    public function actionNumbers() // проверить запрос к бд
    {
        $output = [];
        $data = Yii::$app->getRequest()->getBodyParams();

        $directions = (isset($data['directions']) ? $data['directions'] : []);
        $carrier = (isset($data['carrier']) ? $data['carrier'] : null);

        $query = Flight::find();
        $query = $query->select(['flt']);

        if ($carrier) {
            $query = $query->andWhere(['carrier' => $carrier]);
        }
        $where = [];

        if (count($directions) > 1) {
            $where[] = 'or';

            foreach ($directions as $item) {
                if (isset($item['dep_id']) && isset($item['arr_id'])) {
                    $where[] = [
                        'and',
                        [
                            'dep_airport_id' => $item['dep_id'],
                            'arr_airport_id' => $item['arr_id']
                        ]
                    ];
                } elseif (isset($item['dep_id'])) {
                    $where[] = ['dep_airport_id' => $item['dep_id']];
                } elseif (isset($item['arr_id'])) {
                    $where[] = ['arr_airport_id' => $item['dep_id']];
                }
            }

            if ($where) {
                $query = $query->andWhere($where);
            }
        } else {
            foreach ($directions as $item) {
                if (isset($item['dep_id']) && isset($item['arr_id']) && $item['arr_id'] && $item['dep_id']) {
                    $query = $query->andWhere(
                        [
                            'AND',
                            [
                                'dep_airport_id' => $item['dep_id'],
                                'arr_airport_id' => $item['arr_id']
                            ]
                        ]
                    );
                } elseif (isset($item['dep_id']) && $item['dep_id']) {
                    $query = $query->andWhere(['dep_airport_id' => $item['dep_id']]);
                } elseif (isset($item['arr_id']) && $item['arr_id']) {
                    $query = $query->andWhere(['arr_airport_id' => $item['arr_id']]);
                }
            }
        }

        $query = $query->groupBy(['flt']);
        $query = $query->orderBy(['flt' => 'ASC']);
        $flights = $query->all();

        foreach ($flights as $flight) {
            /** @var Flight $flight */
            $item = new \stdClass();
            $item->flt = $flight->flt;
            $item->fltDes = $flight->flt;
            $item->carrier = $flight->carrier;
            $output[] = $item;
        }

        return $output;
    }

    /**
     * Search flight_id by id string
     * @param $search
     * @return array|null
     */
    public function actionFlightList($search)
    {
        if (!is_null($search)) {
            $search = '' . (int)$search;
            $query = new yii\db\Query;
            $query->select('id')
                ->from('ccp_flight')
                ->where(['like', ' CAST(id AS TEXT) ', $search])->limit(50);
            $command = $query->createCommand();
            try {
                $data = $command->queryAll();
            } catch (yii\db\Exception $e) {
                return NULL;
            }
            return $data;
        }
        return NULL;
    }

}
