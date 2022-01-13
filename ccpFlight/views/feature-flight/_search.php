<?php

use common\modules\ccpFlight\models\FeatureFlight;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;


/* @var $this yii\web\View */
/* @var $model common\modules\ccpFlight\models\search\FeatureFlightSearch */
/* @var $form yii\widgets\ActiveForm */
?>


<div>
    <a href="#" id="search-filter" class="btn btn-primary"> >>> Open Filter </a>
</div>

<div class="flight-search" style="display:none">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'id') ?>

            <?= $form->field($model, 'flight_flt')->dropDownList($model::listFightFlts(),
                ['prompt' => '']
            ) ?>
        </div>

        <div class="col-md-3">
            <?= $form->field($model, 'note') ?>

            <?= $form->field($model, 'type')->dropDownList([null => ''] + FeatureFlight::mapTypes(),
                ['prompt' => '']
            ) ?>
        </div>

        <div class="col-md-3">

            <?= $form->field($model, 'start_date')->widget(DatePicker::classname(), [
                'options' => ['placeholder' => 'Enter  date  ...'],
                'pluginOptions' => [
                    'todayHighlight' => true,
                    'todayBtn' => true,
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                ]
            ])->label("Расчетная  дата отправления "); ?>

            <?= $form->field($model, 'end_date')->widget(DatePicker::classname(), [
                'options' => ['placeholder' => 'Enter  date  ...'],
                'pluginOptions' => [
                    'todayHighlight' => true,
                    'todayBtn' => true,
                    'format' => 'yyyy-M-dd',
                    'autoclose' => true,
                ]
            ])->label("Расчетная дата прибытия"); ?>


        </div>


    </div>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('flight', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('flight', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<div>
    <ul>
        <?php foreach ($model->getAttributes() as $key => $value): ?>
            <?php if ($value): ?>
                <li>
                    <?= Html::activeLabel($model, $key) ?> : <?= $value ?>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</div>
