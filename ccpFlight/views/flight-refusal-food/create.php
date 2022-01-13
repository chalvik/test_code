<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\ccpFlight\models\FlightRefusalFood */

$this->title = Yii::t('FlightRefusalFood', 'Create Flight Refusal Food');
$this->params['breadcrumbs'][] = ['label' => Yii::t('FlightRefusalFood', 'Flight Refusal Foods'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-refusal-food-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
