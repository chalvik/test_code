<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\ccpFlight\models\search\FeatureFlightSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('flight', 'Feature Flights');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="feature-flight-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_search', ['model' => $searchModel]); ?>

    <?php
    $panelBeforeTemplate = '
    <div class="pull-right">{toolbarContainer} </div>
    <div class="pull-left">
    </div>
    <div class="clearfix"></div>
    {pager}
    <div class="clearfix"></div> ';
    ?>

    <?php $gridColumns = [
        ['class' => 'yii\grid\SerialColumn'],

        'id',
        [
            'attribute' => 'flight_flt',
            'format' => 'raw',
            'value' => function ($data) {
                return Html::a($data->flight_flt,
                    ['/admin/flight/default/index',
                        'FlightSearch[flt]' => $data->flight_flt,
                    ], ['data-pjax' => 0]
                );
            }
        ],
        'note:ntext',
        'start_date',
        'end_date',

        ['class' => 'yii\grid\ActionColumn'],
    ]; ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => $gridColumns,
        'containerOptions' => ['style'=>'overflow: auto'], // only set when $responsive = false
        'toolbar' =>  [
            ['content'=>
                Html::a(
                    '<i class="glyphicon glyphicon-plus-sign"></i> '.Yii::t('scheduler', 'Create'),
                    ['create'],
                    ['class' => 'btn btn-success']
                ).
                Html::a(
                    '<i class="glyphicon glyphicon-flash"></i> '.Yii::t('cargo', 'Test API'),
                    ['test'],
                    ['class' => 'btn btn-info']
                ),
                'options' => [
                    'class' => 'btn-group pull-left',
                ],
            ],
            '{export}',
            '{toggleData}',
        ],
        'pjax' => true,
        'bordered' => true,
        'striped' => false,
        'condensed' => false,
        'responsive' => true,
        'hover' => true,
        'panel' => [
            'type' => GridView::TYPE_SUCCESS,
        ],
        'panelBeforeTemplate' => $panelBeforeTemplate,
    ]); ?>

</div>
