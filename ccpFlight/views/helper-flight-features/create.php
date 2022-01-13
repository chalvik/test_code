<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\ccpFlight\models\helpers\HelperFlightFeatures */

$this->title = 'Create Helper Flight Features';
$this->params['breadcrumbs'][] = ['label' => 'Helper Flight Features', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="helper-flight-features-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
