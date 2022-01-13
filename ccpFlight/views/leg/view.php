<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\ccpFlight\models\FlightLeg */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('leg', 'Flight Legs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-leg-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('leg', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('leg', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('leg', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'flight_id',
            'day',
            'flt',
            'dep',
            'dep_airport_id',
            'carrier',
            'legcd',
            'arr',
            'arr_airport_id',
            'ac',
            'reg',
            'aircraft_id',
            'canceled:boolean',
            'adate',
            'aroute',
            'std',
            'sta',
            'etd',
            'eta',
            'blof',
            'tkof',
            'tdown',
            'blon',
            'dep_gate',
            'arr_gate',
            'dep_stand',
            'arr_stand',
            'created_at',
            'updated_at',
            'last_updated_at',
            'last_load_updated_at',
            'last_restriction_updated_at',
        ],
    ]) ?>

</div>
