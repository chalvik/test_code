<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\ccpFlight\models\FlightLegCrew */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('leg_crew', 'Flight Leg Crews'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-leg-crew-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('leg_crew', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('leg_crew', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('leg_crew', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'employee_id',
            'flight_id',
            'roster_id',
            'pos_code',
            'id_dhd',
            'pos_leg1',
            'pos_flight',
            'pos_pu',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
