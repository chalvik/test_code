<?php

use common\modules\food\models\Menu;

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\modules\ccpAircraft\models\Aircraft;
use common\modules\ccpAirport\models\Airport;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model common\modules\ccpFlight\models\Flight */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class ="row">
        <div class="col-md-4">
            <?= $form->field($model, 'day')->textInput() ?>
            <?= $form->field($model, 'flt')->textInput() ?>
            <?= $form->field($model, 'fltDes')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'os')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'carrier')->textInput() ?>
            <?= $form->field($model, 'aircraft_id')->dropDownList(ArrayHelper::map(Aircraft::find()->all(), 'id', 'iata')) ?>
            <?= $form->field($model, 'dep_airport_id')->dropDownList(ArrayHelper::map(Airport::find()->all(), 'id', 'iata')) ?>
            <?= $form->field($model, 'arr_airport_id')->dropDownList(ArrayHelper::map(Airport::find()->all(), 'id', 'iata')) ?>
            <?= $form->field($model, "menu_id")->widget(Select2::className(), [
                'data' => ArrayHelper::map(Menu::find()->all(), 'id', 'title'),
                'options' => ['placeholder' => 'Выберите меню...'],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]); ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'arr_gate')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'dep_gate')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'arr_stand')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'dep_gate')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'interval')->textInput(['maxlength' => true]) ?>
            <?php // = $form->field($model, 'arr_weather')->textInput() ?>
            <?php // = $form->field($model, 'dep_weather')->textInput() ?>
            <?= $form->field($model, 'deleted')->checkbox() ?>
            <?= $form->field($model, 'deleted_at')->textInput() ?>
            <?= $form->field($model, 'deleted_user_id')->textInput() ?>
            <?= $form->field($model, 'canceled')->textInput() ?>
            <?= $form->field($model, 'updated_user_id')->textInput() ?>
        </div>
        <div class="col-md-4">

            <?= $form->field($model, 'std')->widget(DateTimePicker::classname(), [
                'options' => ['placeholder' => 'Enter  date  ...'],
                'pluginOptions' => [
                    'todayHighlight' => true,
                    'todayBtn' => true,
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                ]
            ]); ?>
            <?= $form->field($model, 'sta')->widget(DateTimePicker::classname(), [
                'options' => ['placeholder' => 'Enter  date  ...'],
                'pluginOptions' => [
                    'todayHighlight' => true,
                    'todayBtn' => true,
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                ]
            ]); ?>
            <?= $form->field($model, 'etd')->widget(DateTimePicker::classname(), [
                'options' => ['placeholder' => 'Enter  date  ...'],
                'pluginOptions' => [
                    'todayHighlight' => true,
                    'todayBtn' => true,
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                ]
            ]); ?>
            <?= $form->field($model, 'eta')->widget(DateTimePicker::classname(), [
                'options' => ['placeholder' => 'Enter  date  ...'],
                'pluginOptions' => [
                    'todayHighlight' => true,
                    'todayBtn' => true,
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                ]
            ]); ?>
            <?= $form->field($model, 'blon')->widget(DateTimePicker::classname(), [
                'options' => ['placeholder' => 'Enter  date  ...'],
                'pluginOptions' => [
                    'todayHighlight' => true,
                    'todayBtn' => true,
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                ]
            ]); ?>
            <?= $form->field($model, 'blof')->widget(DateTimePicker::classname(), [
                'options' => ['placeholder' => 'Enter  date  ...'],
                'pluginOptions' => [
                    'todayHighlight' => true,
                    'todayBtn' => true,
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                ]
            ]); ?>
            <?= $form->field($model, 'tkof')->widget(DateTimePicker::classname(), [
                'options' => ['placeholder' => 'Enter  date  ...'],
                'pluginOptions' => [
                    'todayHighlight' => true,
                    'todayBtn' => true,
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                ]
            ]); ?>
            <?= $form->field($model, 'tdown')->widget(DateTimePicker::classname(), [
                'options' => ['placeholder' => 'Enter  date  ...'],
                'pluginOptions' => [
                    'todayHighlight' => true,
                    'todayBtn' => true,
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                ]
            ]); ?>

            <?= $form->field($model, 'estimated')->textInput() ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('flight', 'Create') : Yii::t('flight', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
