<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\ccpFlight\models\FlightLeg */

$this->title = Yii::t('leg', 'Create Flight Leg');
$this->params['breadcrumbs'][] = ['label' => Yii::t('leg', 'Flight Legs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-leg-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
