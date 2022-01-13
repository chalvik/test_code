<?php

namespace common\modules\scheduler\controllers;

use Yii;
use common\modules\scheduler\models\SchedulerTask;
use common\modules\scheduler\models\search\SchedulerTaskSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DefectController implements the CRUD actions for SchedulerTask model.
 * Class DefaultController
 * @package common\modules\scheduler\controllers
 */
class DefaultController extends Controller
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
                    'delete'    => ['POST'],
                    'bulk'      => ['POST'],
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
                } elseif ($action == 'status') {
                    $result = $this->setStatusBulk($ids, $value);
                } elseif ($action == 'inline') {
                    $result = $this->addToLineBulk($ids);
                } elseif ($action == 'removeline') {
                    $result = $this->removeLineBulk($ids);
                }
            }
        }
        return json_encode(["success" => $result]);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        if (Yii::$app->request->isAjax) {
            return $this->renderList();
        } else {
            return $this->redirect(['index']);
        }
    }

    /**
     * @return string
     */
    protected function renderList()
    {
        $searchModel = new SchedulerTaskSearch();
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
     * Lists all SchedulerTask models.
     * @return mixed
     */
    public function actionRealTime()
    {
        $searchModel = new SchedulerTaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->setSort([
            'defaultOrder' => ['id'=>SORT_DESC],
        ]);

        return $this->render('real-time', [
            'data' => $searchModel,
        ]);
    }

    /**
     * Lists all SchedulerTask models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SchedulerTaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->setSort([
            'defaultOrder' => ['id'=>SORT_DESC],
        ]);
        
        return $this->renderList();
    }

    /**
     * Displays a single SchedulerTask model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
//        $model->period = $model->period/60;
        
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new SchedulerTask model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SchedulerTask();

        if ($model->load(Yii::$app->request->post())) {
            $model->period = 60*$model->period;
            
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
            $model->period = $model->period/60;
        }
        
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SchedulerTask model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->period = $model->period/60;
        
        if ($model->load(Yii::$app->request->post())) {
            $model->period = 60*$model->period;
            
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
            $model->period = $model->period/60;
        }
            
            return $this->render('update', [
                'model' => $model,
            ]);
    }

    /**
     * Finds the SchedulerTask model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SchedulerTask the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SchedulerTask::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @param $ids
     * @param $value
     * @return bool
     */
    private function setStatusBulk($ids, $value)
    {
        $result = true;
        if (is_array($ids)) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                foreach ($ids as $id) {
                    $model = SchedulerTask::findOne($id);
                    if ($model && isset(SchedulerTask::$statuses[$value])) {
                        $model->status = $value;
                        $model->save();
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
                    $model = SchedulerTask::findOne($id);
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
     * @param $ids
     * @return bool
     */

    private function addToLineBulk($ids)
    {
        $result = true;
        if (is_array($ids)) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                foreach ($ids as $id) {
                    $model = SchedulerTask::findOne($id);
                    if ($model) {
                        $model->addInLine();
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
     * @param $ids
     * @return bool
     */
    private function removeLineBulk($ids)
    {
        $result = true;
        if (is_array($ids)) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                foreach ($ids as $id) {
                    $model = SchedulerTask::findOne($id);
                    if ($model) {
                        $model->removeFromLine();
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
}
