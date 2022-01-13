<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\ccpFlight\models\FlightRefusalFood */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('FlightRefusalFood', 'Flight Refusal Foods'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="flight-refusal-food-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('FlightRefusalFood', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('FlightRefusalFood', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('FlightRefusalFood', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'flight_id',
            'roster_id',
            'crewMemberFullName',
            'role',
            'status',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
