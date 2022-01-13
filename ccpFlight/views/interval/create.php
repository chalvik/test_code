<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\ccpFlight\models\FlightInterval */

$this->title = Yii::t('flight', 'Create Flight Interval');
$this->params['breadcrumbs'][] = ['label' => Yii::t('flight', 'Flight Intervals'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-interval-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
