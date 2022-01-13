<?php

namespace common\modules\ccpFlight\controllers;

use common\modules\ccpFlight\models\renderers\FlightBriefingRenderer;
use common\modules\storagefiles\models\Storagefiles;
use Mpdf\Mpdf;
use Yii;
use common\modules\ccpFlight\models\FlightBriefings;
use common\modules\ccpFlight\models\search\FlightBriefingsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BriefingsController implements the CRUD actions for FlightBriefings model.
 */
class BriefingsController extends Controller
{
    /**
     * {@inheritdoc}
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
     * Lists all FlightBriefings models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FlightBriefingsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDownload($file_id)
    {

        $file = Storagefiles::findOne($file_id);
        if (file_exists($file->getFullPath())) {
            Yii::$app->response->sendFile($file->getFullPath(), $file->origin_name . "." . $file->extension);

        }
    }

    /**
     * Displays a single FlightBriefings model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Displays a single FlightBriefings model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionReNew($id)
    {
        $model = $this->findModel($id);
        $model->refreshPdf();
        $model->save();

        return $this->redirect('index');

    }
    public function actionPdf($id)
    {
        $model = $this->findModel($id);
        $pdf = new Mpdf(['tempDir' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'mpdf','']);
        $pdf->WriteHTML($this->renderPartial('html',['model' => $model]));
        $pdf->Output('', \Mpdf\Output\Destination::INLINE);


    }

    public function actionHtml($id) {
        $model = $this->findModel($id);
        return $this->render('html',['model' => $model]);
    }


    /**
     * Updates an existing FlightBriefings model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing FlightBriefings model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the FlightBriefings model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FlightBriefings the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FlightBriefings::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
