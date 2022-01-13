<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\modules\ccpAirport\models\Airport;
use common\modules\ccpAircraft\models\Aircraft;
use common\modules\ccpEmployee\models\Employee;
use kartik\date\DatePicker;



/* @var $this yii\web\View */
/* @var $model common\modules\ccpFlight\models\search\FlightSearch */
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
            <?= $form->field($model, 'title') ?>
        </div>

        <div class="col-md-3">
            <?= $form->field($model, 'min') ?>
            <?= $form->field($model, 'max') ?>
        </div>

        <div class="col-md-3">

        </div>

        <div class="col-md-3">

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
        <?php foreach ($model->getAttributes() as $key => $value ): ?>
            <?php if ($value): ?>
                <li>
                    <?=Html::activeLabel($model, $key)?> : <?=$value?>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</div>