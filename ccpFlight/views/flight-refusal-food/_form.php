<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\ccpFlight\models\FlightRefusalFood */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-refusal-food-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'flight_id')->textInput() ?>

    <?= $form->field($model, 'roster_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'crewMemberFullName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'role')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('FlightRefusalFood', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
