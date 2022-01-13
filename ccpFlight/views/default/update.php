<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\ccpFlight\models\Flight */

$this->title = Yii::t('flight', 'Update {modelClass}: ', [
    'modelClass' => 'Flight',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('flight', 'Flights'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('flight', 'Update');
?>
<div class="flight-update">

    <h1>
        <?= Html::encode($this->title) ?>
        <?=Html::img("/img/aircrafts/airbus_m.jpg", ['style'=>"height:50px"]); ?>
    </h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
