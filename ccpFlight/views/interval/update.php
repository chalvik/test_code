<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\ccpFlight\models\FlightInterval */

$this->title = Yii::t('flight', 'Update {modelClass}: ', [
    'modelClass' => 'Flight Interval',
]) . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('flight', 'Flight Intervals'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('flight', 'Update');
?>
<div class="flight-interval-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
