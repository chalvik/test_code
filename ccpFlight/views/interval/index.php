<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\ccpFlight\models\search\FlightIntervalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('flight', 'Flight Intervals');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-interval-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_search', ['model' => $searchModel]); ?>

    <?php   $gridColumns = [
            'id',
            'title',
            'min',
            'max',
            'created_at',
            // 'updated_at',

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

    ]); ?>

</div>
