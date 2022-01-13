<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\ccpFlight\models\helpers\HelperFlightFeatures */

$this->title = 'Update Helper Flight Features: ' . $model->flight_id;
$this->params['breadcrumbs'][] = ['label' => 'Helper Flight Features', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->flight_id, 'url' => ['view', 'id' => $model->flight_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="helper-flight-features-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
