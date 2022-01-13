<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $flightFlt integer|null */

$this->title = Yii::t('flight', 'Feature Flights');
$this->params['breadcrumbs'][] = $this->title;
?>
    <h1><?= Html::encode($this->title) ?></h1>

<?= Html::beginForm() ?>

    <div class="row">
        <div class="col-md-6 form-group">
            <?= Html::label('Flight Flt') ?>
            <?= Html::input('number', 'flightFlt', $flightFlt, ['class' => 'form-control', 'required' => true]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Test', ['class' => 'btn btn-primary']) ?>
    </div>

<?= Html::endForm() ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        'id',
        'flight_flt',
        'note:ntext',
        'start_date',
        'end_date',

        //['class' => 'yii\grid\ActionColumn'],
    ],
]); ?>

