<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\ccpFlight\models\FlightLegCrew */

$this->title = Yii::t('leg_crew', 'Update {modelClass}: ', [
    'modelClass' => 'Flight Leg Crew',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('leg_crew', 'Flight Leg Crews'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('leg_crew', 'Update');
?>
<div class="flight-leg-crew-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
