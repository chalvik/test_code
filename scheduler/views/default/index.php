<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\modules\scheduler\models\SchedulerTaskRun;
use common\modules\scheduler\models\search\SchedulerTaskSearch;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\scheduler\models\search\SchedulerTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->registerJs('
     $(document).on("click", "#btn-bulk-group a",function(event){
        event.preventDefault();
        var id_gridview = "#gridview-tasks-container";
        var Ids = $(id_gridview).yiiGridView("getSelectedRows");
        var action = $(this).data("action");
        var value = $(this).data("val");
         var url = $(this).attr("href");
        
        if(Ids.length > 0){
            if(confirm("Are You Sure To "+action+" Selected Record !")){
                $.ajax({
                    type: "POST",
                    url :  url ,
                    data : {ids: Ids, action:action, value:value},
                    dataType : "JSON",
                    success : function($message) {
                            $.pjax.reload({container:id_gridview+"-container"});
                    }
                });
            }
        } else {
            alert("Please Select Record ");
        }
    });
    
    
//    $(document).ready(function() {
//        // начать повторы с интервалом 2 сек
//        var timerId = setInterval(function() {
//            $.pjax.reload({container:"#gtasks"});
//        }, 5000);
//    });
    
    ', \yii\web\View::POS_END);

?>


<?php
$items = [];
$items[] = Html::a(
    ' <i class="fa fa-remove"></i> Удалить',
    ['bulk'],
    [
        'class' => 'text-red',
        'role' => 'menuitem',
        'data' => [
            'action' => 'delete',
        ],
    ]
);
$items[] = Html::a(
    ' <i class="glyphicon glyphicon-play"></i> Add to line',
    ['bulk'],
    [
        'class' => 'text-green',
        'role' => 'menuitem',
        'data' => [
            'action' => 'inline',
        ],
    ]
);
$items[] = Html::a(
    ' <i class="glyphicon glyphicon-stop"></i> Remove from line',
    ['bulk'],
    [
        'class' => 'text-green',
        'role' => 'menuitem',
        'data' => [
            'action' => 'removeline',
        ],
    ]
);


foreach ($searchModel::$statuses as $key => $value) {
    $items[] = Html::a(
        ' <i class="glyphicon glyphicon-equalizer"></i> Set to :' . $value,
        ['bulk'],
        [
            'role' => 'menuitem',
            'data' => [
                'action' => 'status',
                'val' => $key,
            ],
        ]
    );
}

$panelBeforeButton = Html::tag(
    'div',
    Html::button(
        '<i class="glyphicon glyphicon-fire"></i> <span class="caret"></span>',
        [
            'class' => 'btn btn-default dropdown-toggle',
            'title' => 'Груповые действия',
            'data-toggle' => 'dropdown'
        ]
    ) .
    Html::ul(
        $items,
        [
            'class' => 'dropdown-menu dropdown-menu-left',
            'encode' => false,
        ]
    ),
    [
        'class' => 'btn-group',
        'id' => 'btn-bulk-group'
    ]
);

$panelBeforeTemplate = '
    <div class="pull-right">{toolbarContainer} </div>
    <div class="pull-left">' . $panelBeforeButton . '</div>
    <div class="clearfix"></div>
    {pager}
    <div class="clearfix"></div> ';


$this->title = Yii::t('scheduler', 'Scheduler Tasks');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="scheduler-task-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_search', ['model' => $searchModel]); ?>
    <?php
    $gridColumns = [
        ['class' => 'kartik\grid\CheckboxColumn'],
        [
            'attribute' => 'title',
            'options' => ['']
        ],
        [
            'attribute' => 'period',
            'value' => function ($model) {
                return $model->period / 60;
            },
        ],
        [
            'attribute' => 'progress',
            'value' => function ($model) {
                if ($model->inLine) {
                    $output = $model->progress . '%';
                } else {
                    $output = '-';
                }
                return $output;
            },
        ],
        [
            'attribute' => 'status',
            'value' => function ($model) use ($searchModel) {
                $output = SchedulerTaskSearch::$statuses[$model->status];
                return $output;
            },
            'filter' => ($searchModel::$statuses),
        ],
        [
            'label' => 'Status In line ',
            'value' => function ($model) {
                if ($model->inLine) {
                    $output = SchedulerTaskRun::$statuses[$model->inLine->status];
                } else {
                    $output = '<i class="fa fa-square-o"></i>';
                }

                return $output;
            },
            'format' => 'html'

        ],

        'last_activity_at',
        'last_run_at',
        'last_addline_at',
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view}{update}{delete}{log}',
            'buttons' => [
                'log' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-dashboard"></span>',
                        Url::to(['/admin/scheduler/log/index', 'task_id' => $model->id]), ['class' => 'btn btn-default btn-xs', 'target' => 'blank']);
                },
            ],
        ]
    ]; ?>



    <?php // Pjax::begin(['id' => 'gtasks', 'timeout' => false, 'enablePushState' => false]) ?>
    <?= GridView::widget([
        'id' => 'gridview-tasks-container',
        'rowOptions' => function ($model) {
            /** @var \common\modules\scheduler\models\SchedulerTask $model */
            return (time() - strtotime($model->last_activity_at . " UTC") > 20 * 60) ? ['class' => 'warning'] : [];
        },

        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
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
//        'floatHeader' => true,
//        'floatHeaderOptions' => ['scrollingTop' => $scrollingTop],
//        'showPageSummary' => true,
        'panel' => [
            'type' => GridView::TYPE_SUCCESS,
            'options' => ['style' => 'width:100%'],
        ],
        'panelBeforeTemplate' => $panelBeforeTemplate,
    ]);
    ?>

    <?php // Pjax::end()?>

</div>
