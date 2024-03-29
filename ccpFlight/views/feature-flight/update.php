<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\ccpFlight\models\FeatureFlight */

$this->title = Yii::t('flight', 'Update {modelClass}: ', [
    'modelClass' => 'Feature Flight',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('flight', 'Feature Flights'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('flight', 'Update');
?>
<div class="feature-flight-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
