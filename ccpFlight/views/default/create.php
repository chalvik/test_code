<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\ccpFlight\models\Flight */

$this->title = Yii::t('flight', 'Create Flight');
$this->params['breadcrumbs'][] = ['label' => Yii::t('flight', 'Flights'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-create">

    <h1>
        <?=Html::img("/img/aircrafts/airbus_m.jpg", ['style'=>"height:50px"]); ?>
        <?= Html::encode($this->title) ?>
    </h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
