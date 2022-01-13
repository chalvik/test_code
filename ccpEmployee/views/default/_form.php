<?php

use common\modules\library\models\Library;

use kartik\select2\Select2;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\ccpEmployee\models\Employee */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="employee-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'roster_id')->textInput() ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fio_eng')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fio_rus')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'quals_list')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'langs_list')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'port_base')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'block_hours')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'file_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList(\common\modules\ccpEmployee\models\Employee::$list_types) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, "documents")->widget(Select2::className(), [
        'data' => ArrayHelper::map(Library::find()->all(), 'id', 'title'),
        'options' => ['placeholder' => 'Выберите документы...'],
        'pluginOptions' => [
            'multiple' => true,
            'allowClear' => true
        ],
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('employee', 'Create') : Yii::t('employee', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
