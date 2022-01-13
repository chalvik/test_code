<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\ccpFlight\models\FlightLeg */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-leg-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'flight_id')->textInput() ?>

    <?= $form->field($model, 'day')->textInput() ?>

    <?= $form->field($model, 'flt')->textInput() ?>

    <?= $form->field($model, 'dep')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'dep_airport_id')->textInput() ?>

    <?= $form->field($model, 'carrier')->textInput() ?>

    <?= $form->field($model, 'legcd')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'arr')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'arr_airport_id')->textInput() ?>

    <?= $form->field($model, 'ac')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'reg')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'aircraft_id')->textInput() ?>

    <?= $form->field($model, 'canceled')->checkbox() ?>

    <?= $form->field($model, 'adate')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'aroute')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'std')->textInput() ?>

    <?= $form->field($model, 'sta')->textInput() ?>

    <?= $form->field($model, 'etd')->textInput() ?>

    <?= $form->field($model, 'eta')->textInput() ?>

    <?= $form->field($model, 'blof')->textInput() ?>

    <?= $form->field($model, 'tkof')->textInput() ?>

    <?= $form->field($model, 'tdown')->textInput() ?>

    <?= $form->field($model, 'blon')->textInput() ?>

    <?= $form->field($model, 'arr_stand')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'dep_gate')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'dep_gate')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'arr_gate')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('leg', 'Create') : Yii::t('leg', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
