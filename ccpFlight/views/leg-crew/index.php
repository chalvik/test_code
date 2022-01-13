<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\ccpFlight\models\search\FlightLegCrewSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('leg_crew', 'Flight Leg Crews');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-leg-crew-index">

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

    <?php   $gridColumns = [
        'id',
        'employee_id',
        [
            'attribute' => 'flight_id',
            'format' => 'raw',
            'value' => function ($data) {
                return Html::a($data->flight_id, 
                    [
                        '/admin/flight/default/index',
                        'FlightSearch[id]' => $data->flight_id,
                    ],
                    [
                        'data-pjax' => 0,
                    ]
                );
            }
        ],
        [
            'attribute' => 'roster_id',
            'format' => 'raw',
            'value' => function ($data) {
                return Html::a($data->roster_id,
                    [
                        '/admin/employee/default/index',
                        'EmployeeSearch[roster_id]' => $data->roster_id
                    ],
                    [
                        'data-pjax' => 0,
                        'title' => $data->employee->fio_rus ?? null
                    ]
                );
            }
        ],
        'pos_code',
        'last_updated_at',
        'changed_at',
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
