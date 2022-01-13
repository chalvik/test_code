<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\ccpFlight\models\FlightLeg */

$this->title = Yii::t('leg', 'Update {modelClass}: ', [
    'modelClass' => 'Flight Leg',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('leg', 'Flight Legs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('leg', 'Update');
?>
<div class="flight-leg-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
