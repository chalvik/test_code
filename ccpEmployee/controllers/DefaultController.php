<?php
namespace common\modules\ccpEmployee\controllers;

use common\modules\ccpEmployee\models\Employee;
use common\modules\ccpEmployee\models\search\EmployeeSearch;
use Yii;
use common\components\BaseController;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * Контроллер для админки разработчика (backend)
 * реализует методы для работы, с сотрудниками
 *
 * Class DefaultController
 * @package common\modules\ccpEmployee\controllers
 * @property string $modelName
 */
class DefaultController extends BaseController
{

    public $modelName = "Employee";

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();
        $actions['get'] = [
            'class' => 'common\modules\storagefiles\actions\StreamAction',
        ];
        return $actions;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Выводит список сотрудников
     * Lists all Employee models.
     * @return mixed
     */
    public function actionIndex()
    {
        /** @var EmployeeSearch $searchModel */
        $searchModel = $this->createSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Получить сотрудника по табельному номеру
     * Displays a single Employee model for roster_id.
     * @param $roster_id
     * @return string
     * @throws NotFoundHttpExceptionотчетов
     */
    public function actionRosterView($roster_id)
    {
        $model = Employee::find()->where(['roster_id' => $roster_id])->one();
        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

}
