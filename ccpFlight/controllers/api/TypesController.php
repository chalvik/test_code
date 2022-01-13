<?php

namespace common\modules\ccpFlight\controllers\api;

use common\modules\ccpFlight\models\FlightTypes;
use Yii;
use api\rest\ActiveRestController;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

/**
 * Класс реализует методы для обработки апи запросов
 * для типов рейса рейсов
 *
 * This is the api controller class for flights types.
 * Class TypesController    /**
 * @package common\modules\ccpFlight\controllers\api
 * @property string $modelClass
 */

class TypesController extends ActiveRestController
{

    public $modelClass = 'common\modules\ccpFlight\models\FlightTypes';

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
        return [
            'index' => ['GET', 'HEAD'],
        ];;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();
        // customize the data provider preparation with the "prepareDataProvider()" method
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        unset($actions['update'], $actions['view'], $actions['create']);
        return $actions;
    }

    /**
     * @inheritdoc
     * @throws NotFoundHttpException
     */
    public function prepareDataProvider()
    {
        return FlightTypes::$type_list;
    }

}
