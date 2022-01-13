<?php

use common\modules\ccpFlight\models\search\FlightSearch;
use common\modules\EdbPassenger\models\EdbPassengerGroup;
use common\modules\EdbPassenger\models\helpers\FlightPassengerAllCodes;
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\modules\ccpAircraft\models\Aircraft;
use kartik\date\DatePicker;


/* @var $this yii\web\View */
/* @var $model common\modules\ccpFlight\models\search\FlightSearch */
/* @var $form yii\widgets\ActiveForm */

?>

    <!-- Button trigger modal -->
    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">SMART SEARCH <span id="count"
                                                                                 class="label label-success"></span>
                    </h4>
                </div>
                <div class="modal-body">
                    <div>
                        <?= $form->field($model, 'std_from')->widget(DateTimePicker::classname(), [
                            'options' => ['placeholder' => 'Enter  date  ...'],
                            'pluginOptions' => [
                                'todayHighlight' => true,
                                'todayBtn' => true,
                                'format' => 'yyyy-mm-dd hh:ii',
                                'autoclose' => true,
                            ]
                        ])->label("Плановая  дата отправления (от)"); ?>

                        <?= $form->field($model, 'std_to')->widget(DateTimePicker::classname(), [
                            'options' => ['placeholder' => 'Enter  date  ...'],
                            'pluginOptions' => [
                                'todayHighlight' => true,
                                'todayBtn' => true,
                                'format' => 'yyyy-mm-dd hh:ii',
                                'autoclose' => true,
                            ]
                        ])->label("Плановая  дата отправления (до)"); ?>

                        <?= $form->field($model, 'code_search_strategy')->dropDownList(FlightSearch::mapStrategy()) ?>

                        <?php if ($model->codes_existed)
                            echo implode(" ",
                                    array_map(function ($model) {
                                        return Html::tag('span', $model, ['class' => 'label label-success', 'style' => ['font-size' => '15px']]);
                                    }, $model->codes_existed)
                                ) . "<br>";
                        ?>
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation">
                                <a href="#existed" class="active" aria-controls="home" role="tab"
                                   data-toggle="tab">Existed</a>
                            </li>
                            <li role="presentation">
                                <a href="#not_existed" aria-controls="home" role="tab" data-toggle="tab">Not Existed</a>
                            </li>

                            <li role="presentation">
                                <a href="#other" aria-controls="home" role="tab" data-toggle="tab">Другое</a>
                            </li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <?php /** @var \common\modules\EdbPassenger\models\EdbPassengerCode $group */ ?>
                            <div role="tabpanel" class="tab-pane active" id="existed">

                                <?php
                                if ($codesGroups = EdbPassengerGroup::find()->with('codes')->all()) {
                                    foreach ($codesGroups as $group) { ?>
                                        <?php if ($codes = $group->codes) { ?>
                                            <a role="button" class="btn" data-toggle="collapse"
                                               href="#codesGroup<?= $group->id; ?>"
                                               aria-expanded="false" aria-controls="collapseExample">
                                                <?= $group->title; ?> (<?= count($codes); ?>)
                                            </a>

                                            <div class="collapse" id="codesGroup<?= $group->id; ?>">
                                                <?php foreach ($codes as $code) { ?>
                                                    <div class="checkbox">
                                                        <label class="click_selected" data-value="<?= $code->code; ?>">
                                                            <?php echo Html::checkbox(
                                                                'codes_existed[]',
                                                                in_array($code->code, $model->codes_existed),
                                                                ['id' => $code->id, 'value' => $code->code]
                                                            ); ?>
                                                            <?= $code->code; ?> - <?= $code->title; ?>
                                                        </label>

                                                    </div>
                                                    <?php
                                                } ?>
                                            </div>
                                        <?php } ?>

                                    <?php }
                                }

                                ?></div>
                            <div role="tabpanel" class="tab-pane" id="not_existed">

                                <?php if ($codes = FlightPassengerAllCodes::find()->where(['<>', 'is_existed', true])->all()) {
                                    foreach ($codes as $code) { ?>
                                        <label>
                                            <?php echo Html::checkbox(
                                                'codes[]',
                                                in_array($code->code, $model->codes),
                                                ['value' => $code->code, 'data' => ['value' => $code->code]]
                                            ); ?>
                                            <?= $code->code; ?>
                                        </label>

                                        <?php
                                    }
                                } ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="other">
                                <label class="click_selected">
                                    Опоздавшие на трансфер пассажиры
                                    <?= $form->field($model, 'has_fail_transfer')->checkbox(); ?>
                                </label>
                                <?php Yii::error($model->crew_pos);?>
                                <?= $form->field($model, 'crew_pos')->dropDownList([null => ''] + FlightSearch::mapCrewPos(),
                                    ['class' => 'form-control select2 click_selected','multiple' => true,'style' => 'width:200px; !important']) ?>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <?= Html::submitButton(Yii::t('flight', 'Search'), ['class' => 'btn btn-primary']) ?>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

<?php
$js = <<<JS
$('.click_selected').on('change',function() {
  let form_data = $("#flight_search_form").serialize();
    $('#count').load("/admin/flight/default/smart-search?" + form_data);
  //  console.log($('#flight_search_form').serialize());
});
JS;

Yii::$app->view->registerJs($js, 4);
