<?php

use kartik\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\ccpFlight\models\search\FlightLegSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('leg', 'Flight Legs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-leg-index">

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
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'flight_id',
            'day',
            'flt',
            'dep',
            'reg',
            'aircraft_id',
            'std',
            'etd',
            'blof',
            'tkof',

            'last_updated_at',
            'last_load_updated_at',
            'last_restriction_updated_at',

            ['class' => 'yii\grid\ActionColumn'],
    ]; ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
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
        'type' => GridView::TYPE_PRIMARY,
        ],
        'panelBeforeTemplate' => $panelBeforeTemplate,
    ]);

    ?>


</div>
