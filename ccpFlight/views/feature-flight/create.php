<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\ccpFlight\models\FeatureFlight */

$this->title = Yii::t('flight', 'Create Feature Flight');
$this->params['breadcrumbs'][] = ['label' => Yii::t('flight', 'Feature Flights'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="feature-flight-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
