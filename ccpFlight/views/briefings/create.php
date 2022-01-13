<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\ccpFlight\models\FlightBriefings */

$this->title = 'Create Flight Briefings';
$this->params['breadcrumbs'][] = ['label' => 'Flight Briefings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-briefings-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
