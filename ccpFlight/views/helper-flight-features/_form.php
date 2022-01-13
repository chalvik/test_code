<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\ccpFlight\models\helpers\HelperFlightFeatures */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="helper-flight-features-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'flight_id')->textInput() ?>

    <?= $form->field($model, 'codes')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'existed_codes')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'has_fail_transfers')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
