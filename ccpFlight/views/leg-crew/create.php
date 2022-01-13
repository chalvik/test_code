<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\ccpFlight\models\FlightLegCrew */

$this->title = Yii::t('leg_crew', 'Create Flight Leg Crew');
$this->params['breadcrumbs'][] = ['label' => Yii::t('leg_crew', 'Flight Leg Crews'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-leg-crew-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
