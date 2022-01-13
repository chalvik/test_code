<?php

namespace common\modules\scheduler\controllers;

use Yii;
use common\modules\scheduler\models\SchedulerTaskLog;
use common\modules\scheduler\models\search\SchedulerTaskLogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LogController implements the CRUD actions for SchedulerTaskLog model.
 * Class LogController
 * @package common\modules\scheduler\controllers
 */
class LogController extends Controller
{
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
                    'delete-all' => ['POST'],
                ],
            ],
        ];
    }

    /**
     *  Action Multi Action for GridView
     */
    public function actionBulk()
    {
        $result = false;
        if (Yii::$app->request->isAjax) {
            $ids = Yii::$app->request->post('ids', []);
            $action = Yii::$app->request->post('action');
            $value = Yii::$app->request->post('value');

            if ($ids && is_array($ids)) {
                if ($action == 'delete') {
                    $result = $this->deleteBulk($ids);
                }
            }
            return json_encode(["success" => $result]);
        }
    }


    /**
     * Lists all SchedulerTaskLog models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SchedulerTaskLogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->setSort([
            'defaultOrder' => ['id'=>SORT_DESC],
        ]);
                
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SchedulerTaskLog model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new SchedulerTaskLog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SchedulerTaskLog();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing SchedulerTaskLog model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing SchedulerTaskLog model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     *   Deletes all record.
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDeleteAll()
    {
        if (Yii::$app->request->isPost) {
            SchedulerTaskLog::deleteAll();
            return $this->redirect(['index']);
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Finds the SchedulerTaskLog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SchedulerTaskLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SchedulerTaskLog::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @param $ids
     * @return bool
     */
    private function deleteBulk($ids)
    {
        $result = true;
        if (is_array($ids)) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                foreach ($ids as $id) {
                    $model = SchedulerTaskLog::findOne($id);
                    if ($model) {
                        $model->delete();
                    }
                }
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                $result = false;
            }
        }
        return $result;
    }

    /**
     * ВЫводит краткую инфрормацию о рейсе, для аякс запроса в Grid
     * @return string
     */
    public function actionGridView()
    {
        $id = Yii::$app->request->post('expandRowKey');
        $model = $this->findModel($id);
        return Yii::$app->controller->renderPartial('_view/_grid_view', ['model'=>$model]);
    }

}
