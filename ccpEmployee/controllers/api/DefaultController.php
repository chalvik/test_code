<?php
namespace common\modules\ccpEmployee\controllers\api;

use common\modules\ccpEmployee\models\Employee;
use Yii;
use api\rest\ActiveRestController;
use common\modules\library\models\Library;
use common\modules\library\models\LibraryEmployee;
use yii\data\ActiveDataProvider;

/**
 * Класс реализует методы для обработки апи запросов
 * для базы сотрудников
 *
 * This is the api controller class for Emplyee
 * Class DefaultController
 * @package common\modules\ccpEmployee\controllers\api
 * @property string $modelClass
 */
class DefaultController extends ActiveRestController
{

    public $modelClass = 'common\modules\ccpEmployee\models\Employee';

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        $verbs = parent::verbs();
        $verbs['roster-id'] = ['GET', 'HEAD'];
        $verbs['list'] = ['GET', 'HEAD'];
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
        unset($actions['update'], $actions['create']);
        return $actions;
    }


    /**
     * Предобработка данных перед методомо actionIndex
     * prepare data for action index
     * @return object
     */
    public function prepareDataProvider()
    {
        /** @var  Employee $modelClass */
        $modelClass = $this->modelClass;
        $query = $modelClass::find();

        return Yii::createObject([
            'class' => ActiveDataProvider::className(),
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC,
                    ]
            ],
            'pagination' => [
                'pageSize' => 1000,
            ],
        ]);
    }
    

    /**
     * Поиск сотрудника по табельному номеру
     * Возвращает объект сотрудника
     * Get record Employee for roster_id
     *
     * @param int $roster_id
     * @return \common\modules\ccpEmployee\models\Employee
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionRosterId($roster_id)
    {

        /** @var Employee $model */
        /** @var Employee $modelClass */
        $modelClass = $this->modelClass;
        $model = $modelClass::findOne(['roster_id'=>$roster_id]);

        if (!$model) {
            throw new \yii\web\NotFoundHttpException("Page not found Employee for roster");
        }
        return $model;
    }

    /**
     * Поиск сотрудка по части строки
     * поиск производится по табельному, по фио , на англиском и русском языках
     *
     * Get list for filter
     * @param string $term string for find
     * @return string
     */
    public function actionList($term)
    {
        /** @var Employee $modelClass */
        $modelClass = $this->modelClass;
        return $modelClass::find()
                ->orFilterWhere(['roster_id'=>(int)$term])
                ->orFilterWhere(['like', 'LOWER(fio_eng)', mb_strtolower($term)])
                ->orFilterWhere(['like', 'LOWER(fio_rus)', mb_strtolower($term)])
                ->orderBy(['fio_rus' => 'asc'])
                ->all();
    }


    // To do Удалить метод так как он перенесен в library/controllers/api/DefaultController
    /**
     * Обновояет статус прочтения документом в библиотеке
     * ===  перенесено в library/controllers/api/DefaultController
     *
     * Updates all statuses of Employee->Library relations
     * @param integer $employee_id ID of Employee
     * @return array
     */
    public function actionUpdateReadStatus($employee_id)
    {
        $response = [];
        foreach (Yii::$app->request->bodyParams as $data) {
            $documents = Library::find()->where(['id' => $data['libraries']])->all();
            foreach ($documents as $number => $document) {
                if (!$document->publish_flag) {
                    unset($documents[$number]);
                }

                $model = LibraryEmployee::find()->where([
                    'employee_id' => $employee_id,
                    'library_id' => $document->id,
                ])->one();

                if ($model) {
                    $model->flag_new = $data['flag_new'];
                } else {
                    $model = new LibraryEmployee;
                    $model->employee_id = $employee_id;
                    $model->library_id = $document->id;
                    $model->flag_new = $data['flag_new'];
                }
                $model->save();
                $response[] = $model;
            }
        }
        return $response;
    }
}
