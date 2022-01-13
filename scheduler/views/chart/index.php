<?php
/**
 * Created by PhpStorm.
 * User: Chernogor Alexey
 * Email: achernogor@iseck.com
 * Date: 18.02.18
 * Time: 3:54
 */
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\Html;

?>

<div>

    <h1>Scheduler  statistic </h1>

    <section>
        <ul class="nav nav-tabs control-sidebar-tabs">
            <li class="active">
                <a href="#flight-wid-tab" data-toggle="tab" aria-expanded="true">
                    Day
                </a>
            </li>
            <li>
                <a href="#flight-cargo-tab" data-toggle="tab">
                    Week
                </a>
            </li>
            <li>
                <a href="#flight-chat-tab" data-toggle="tab">
                    Month
                </a>
            </li>
        </ul>
    </section>

    <?php $form = ActiveForm::begin(); ?>
    <?php
        echo DatePicker::widget([
            'name' => 'date',
            'type' => DatePicker::TYPE_INPUT,
            'value' => date("d-m-Y", strtotime($date)),
            'pluginOptions' => [
                'autoclose'=>true,
                'format' => 'dd-mm-yyyy'
            ]
        ]);
        ?>
        <?=Html::submitButton('Отправить'); ?>
    <?php ActiveForm::end(); ?>


<?php
echo \bburim\flot\Chart::widget([
    'data' => [
        [
            'label' => 'Success',
            'data'  => $data['ok'],
            'bars'  => ['show' => true],
        ],
        [
          'label' => 'Errors',
            'data'  => $data['error'],
            'bars'  => ['show' => true],
        ],
        [
            'label' => 'Add Line Ok',
            'data'  => $data['line_ok'],
            'bars'  => ['show' => true],
        ],
        [
            'label' => 'Add Line Errors',
            'data'  => $data['line_error'],
            'bars'  => ['show' => true],
        ],


    ],
    'options' => [
        'xaxis' => [
//            'timeformat'=>"%Y/%m/%d",
//          'position' => 'bottom',
          'ticks' => $x,
//            'tickFormatter' =>
        ],
        'legend' => [
            'position'          => 'nw',
            'show'              => true,
            'margin'            => 10,
            'backgroundOpacity' => 0.5
        ],
    ],
    'htmlOptions' => [
        'style' => 'width:100%;height:400px;'
    ]
]);


?>

</div>
