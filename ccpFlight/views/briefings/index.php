<?php

use common\modules\ccpFlight\models\FlightBriefings;
use common\modules\report\models\ReportExport;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\ccpFlight\models\search\FlightBriefingsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Flight Briefings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-briefings-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'attribute' => 'flight_id',
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::a($data->flight_id,
                        ['/admin/flight/default/index',
                            'FlightSearch[id]' => $data->flight_id,
                        ], ['data-pjax' => 0]
                    );
                }
            ],
            'title',
            'std',
            'flt',
            [
                'label' => 'Скачать',
                'format' => 'html',
                'value' => function (FlightBriefings $model) {
                    if ($model->file_id) return Html::a('Download', Url::to(['download', 'file_id' => $model->file_id]), ['target' => '_blank']);
                }
            ],[
                'label' => 'Обновить',
                'format' => 'html',
                'value' => function (FlightBriefings $model) {
                    return Html::a('new', Url::to(['briefings/re-new', 'id' => $model->id]), ['target' => '_blank']);
                }
            ],[
                'label' => 'Html',
                'format' => 'html',
                'value' => function (FlightBriefings $model) {
                    return Html::a('html', Url::to(['briefings/html', 'id' => $model->id]), ['target' => '_blank']);
                }
            ],[
                'label' => 'pdf',
                'format' => 'html',
                'value' => function (FlightBriefings $model) {
                    return Html::a('pdf', Url::to(['briefings/pdf', 'id' => $model->id]), ['target' => '_blank']);
                }
            ],
            'status',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
