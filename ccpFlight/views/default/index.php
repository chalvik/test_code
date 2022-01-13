<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\ccpFlight\models\search\FlightSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('flight', 'Flights');
$this->params['breadcrumbs'][] = $this->title;
?>


    <div class="flight-index">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= $this->render('_search', ['model' => $searchModel]); ?>

        <?php $gridColumns = [
            [
                'class' => 'kartik\grid\ExpandRowColumn',
                'expandAllTitle' => 'Expand all',
                'collapseTitle' => 'Collapse all',
                'expandIcon' => '<span class="glyphicon glyphicon-expand"></span>',
                'value' => function () {
                    return GridView::ROW_COLLAPSED;
                },
                'detailUrl' => ['grid-view'],
                'detailOptions' => [
                    'class' => 'kv-state-enable',
                ],
            ],
            'id',
            'os',
            [
                'attribute' => 'flt',
                'value' => (function ($model) {
                    $output = Html::tag('div', '<b>flt:</b>' . Html::tag('p', $model->flt));
                    $output .= Html::tag('div', '<b>fltDes:</b>' . Html::tag('p', $model->fltDes));
                    return $output;
                }),
                'format' => 'html'
            ],
            [
                'attribute' => 'aircraft_id',
                'value' => (function ($model) {
                    $output = Html::tag('p', 'id:' . $model->aircraft_id);
                    if ($model->aircraft) {
                        $output .= Html::a(
                            'reg:' . $model->aircraft->reg,
                            ['/admin/aircraft/default/view', 'id' => $model->aircraft->id]
                        );
                    }
                    return $output;
                }),
                'format' => 'html'
            ],

            [
                'label' => ' Планируемое ',
                'attribute' => 'std',
                'value' => (function ($model) {
                    /** @var \common\modules\ccpFlight\models\Flight $model */
                    $output = Html::tag('div', '<b>origin std:</b>' . Html::tag('p', $model->origin_std_date));
                    $output .= Html::tag('div', '<b>std:</b>' . Html::tag('p', $model->std));
                    $output .= Html::tag('div', '<b>sta:</b>' . Html::tag('p', $model->sta));
                    return $output;
                }),
                'format' => 'html',
                'filter' => '',
            ],
            [
                'label' => ' Расчетное ',
                'attribute' => 'etd',
                'value' => (function ($model) {
                    /** @var \common\modules\ccpFlight\models\Flight $model */
                    $output = Html::tag('div', '<b>etd:</b>' . Html::tag('p', $model->etd));
                    $output .= Html::tag('div', '<b>eta:</b>' . Html::tag('p', $model->eta));
                    return $output;
                }),
                'format' => 'html',
                'filter' => '',
            ],
            [
                'label' => 'Airports',
                'value' => (function ($model) {
                    /** @var \common\modules\ccpFlight\models\Flight $model */
                    $output = '';
                    if (isset($model->depAirport->iata)) {
                        $output .= Html::tag(
                            'div',
                            '<b>Dep:</b>' . Html::tag('p', $model->depAirport->iata . ' Gate(' . $model->dep_gate . ')')
                        );
                    }
                    if (isset($model->arrAirport->iata)) {
                        $output .= Html::tag(
                            'div',
                            '<b>Arr:</b>' . Html::tag('p', $model->arrAirport->iata . ' Gate(' . $model->arr_gate . ')')
                        );
                    }
                    return $output;
                }),
                'format' => 'html'
            ],
            [
                'label' => 'Экипаж',
                'attribute' => 'crew_roster_id',
                'value' => (function ($model) {
                    /** @var \common\modules\ccpFlight\models\Flight $model */
                    if (count($model->crew)) {
                        $roster_id = Yii::$app->request->get("FlightSearch");
                        $roster_id = $roster_id['crew_roster_id']??null; //@NOTE error because key may not exists
                        return $this->render('_item_employee', [
                            'model' => $model,
                            'roster_id' => $roster_id,
                        ]);
                    }
                    return null;
                }),
                'format' => 'raw',
                'filter' => null
            ],


            [
                'label' => 'Passengers',
                'value' => (function ($model) {
//                $count_transfer = count($model->transfer);
                    $output = "";
                    $output .= Html::a(
                        '<i class="fa fa-users text-green fa-2x"></i> ',
                        ['/admin/passenger/default/index',
                            'EdbPassengerSearch[FLT]' => $model->flt,
                            'EdbPassengerSearch[STD_UTC]' => $model->std
                        ],
                        ['title' => "Passengers"]
                    );
//                $output .= '<div>';
//                $output .= Html::a(
//                    '<i class="fa fa-random  text-blue fa-2x"></i> '.$count_transfer,
//                    ['/admin/passenger/transfer/index', 'FlightPassengerTransferSearch[flight_id]'=>$model->id],
//                    ['title'=>"Transfer"]
//                );
//                $output .= '</div>';
                    return Html::tag('div', $output);
                }),
                'format' => 'raw',
                'filter' => null
            ],

            ['class' => 'yii\grid\ActionColumn'],


        ];


        $panelBeforeButton = Html::a(
            '<i class="glyphicon glyphicon-trash"></i>',
            ['bulk'],
            [
                'data-pjax' => 0,
                'class' => 'btn btn-default',
                'style' => 'display:none',
            ]
        );

        $panelBeforeTemplate = '
        <div class="pull-right">{toolbarContainer} </div>
        <div class="pull-left">
            <span class="btn" style="background: #fcf8e3"> Задержка рейса </span>
            <span class="btn" style="background: #f2dede"> Нет экипажа</span>
            <span class="btn" style="background: #d9edf7"> В полете</span>
            <span class="btn" style="background: #dff0d8"> Выполнен</span>
            <span class="btn" style="background: #ffffb5"> Отменен</span>
            ' . $panelBeforeButton . '</div>
        <div class="clearfix"></div>
        {pager}
        <div class="clearfix"></div> ';
        ?>

        <div>

        </div>

        <?php
        echo GridView::widget([
            'rowOptions' => function ($model) {
                $output = [];

                $current = time();
                $sta = ($model->sta) ? strtotime($model->sta . ' UTC') : 0;
                $eta = ($model->eta) ? strtotime($model->eta . ' UTC') : 0;
                $blof = ($model->blof) ? strtotime($model->blof . ' UTC') : 0;
                $blon = ($model->blon) ? strtotime($model->blon . ' UTC') : 0;

                if ($model->canceled > 0) {
                    $output = ['style' => 'background: #ffffb5'];
                } elseif (!count($model->crew)) {
                    $output = ['class' => 'danger'];
                } elseif ($blof && !$blon && $blof < $current) {
                    $output = ['class' => 'info'];
                } elseif ($blon && $blon < $current) {
                    $output = ['class' => 'success'];
                } elseif ($sta && $sta < $current && $eta < $current) {
                    $output = ['class' => 'warning'];
                }
                return $output;
            },
            'dataProvider' => $dataProvider,
            'columns' => $gridColumns,
            'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
            'toolbar' => [
                ['content' =>
                    Html::a(
                        '<i class="glyphicon glyphicon-plus-sign"></i> ' . Yii::t('scheduler', 'Create'),
                        ['create'],
                        ['class' => 'btn btn-success']
                    ),

                    'options' => [
                        'class' => 'btn-group pull-left',
                    ],
                ],
                '{export}',
                '{toggleData}',
            ],
            'pjax' => true,
            'bordered' => true,
            'striped' => false,
            'condensed' => false,
            'responsive' => true,
            'hover' => true,
            'panel' => [
                'type' => GridView::TYPE_SUCCESS,
            ],
            'panelBeforeTemplate' => $panelBeforeTemplate,
        ]);

        ?>


    </div>

<?php
$js = <<<JS
$( "button#force_update_button" ).click(function( e ) {
    e.preventDefault();
    var flight_id = $(this).data('flight_id');
      $.ajax({
        type: "POST",
        url: '/admin/flight/default/force-update',
        data: {flight_id: flight_id},
        success: function(data)
        {
           console.log('ok');
        }
    });
    $(this).attr('disabled',true);
});

JS;

$this->registerJs($js, 4);
