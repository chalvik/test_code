<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\ccpFlight\models\FlightLegCrew */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-leg-crew-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'employee_id')->textInput() ?>

    <?= $form->field($model, 'flight_id')->textInput() ?>

    <?= $form->field($model, 'roster_id')->textInput() ?>

    <?= $form->field($model, 'pos_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'id_dhd')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pos_leg1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pos_flight')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pos_pu')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('leg_crew', 'Create') : Yii::t('leg_crew', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
