<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use common\modules\scheduler\models\SchedulerTask;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\scheduler\models\search\SchedulerTaskLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('scheduler', 'Scheduler Task Logs');
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs('
     $(document).on("click", "#btn-bulk-group a",function(event){
        event.preventDefault();
        var id_gridview = "#gridview-tasks-log";
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
    ', \yii\web\View::POS_READY);



?>
<div class="scheduler-task-log-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_search', ['model' => $searchModel]); ?>

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

    $panelBeforeButton = Html::tag(
        'div',
        Html::button(
            '<i class="glyphicon glyphicon-fire"></i> <span class="caret"></span>',
            [
                'class' => 'btn btn-default dropdown-toggle',
                'title' => 'Груповые действия',
                'data-toggle' => 'dropdown'
            ]
        ).
        Html::ul(
            $items,
            [
                'class'=>'dropdown-menu dropdown-menu-left',
                'encode'=> false,
            ]
        ),
        [
            'class'=>'btn-group',
            'id'=>'btn-bulk-group'
        ]
    );
    $panelBeforeTemplate = '
    <div class="pull-right">{toolbarContainer} </div>
    <div class="pull-left">'.$panelBeforeButton.'</div>
    <div class="clearfix"></div>
    {pager}
    <div class="clearfix"></div> ';

    ?>



    <?php $gridColumns = [
        ['class' => 'kartik\grid\CheckboxColumn'],
        [
            'class' => 'kartik\grid\ExpandRowColumn',
            'expandAllTitle' => 'Expand all',
            'collapseTitle' => 'Collapse all',
            'expandIcon'=>'<span class="glyphicon glyphicon-expand"></span>',
            'value' => function () {
                return GridView::ROW_COLLAPSED;
            },
            'detailUrl' => ['grid-view'],

            'detailOptions'=>[
                'class'=> 'kv-state-enable',
            ],
        ],

        'id',

        [
            'attribute' => 'task_id',
            'value' => function ($model) {
                $output = "None";
                if ($model->run) {
                    $output = Html::a($model->run->task->title, ["/admin/scheduler/default","SchedulerTaskSearch[id]"=>$model->run->task_id]);
                }
                return $output;
            },
            'format' => 'html',
            'filter' => ArrayHelper::map(SchedulerTask::find()->all(), 'id', 'title'),
        ],

        'task_run_id',
//            [
//                'label' => 'Task Title',
//                'value' => function($model) {
//                   return ($model->run)?$model->run->task->title:"None";
//                }
//            ],
        [
            'attribute' => 'status',
            'value' => function ($model) use ($searchModel) {
                return $searchModel::$statuses[$model->status];
            },
            'filter' => $searchModel::$statuses,
        ],

        'created_at',

        //'status',
        ['class' => 'yii\grid\ActionColumn'],
    ]; ?>

    <?= GridView::widget([
        'id' => 'gridview-tasks-log',
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => $gridColumns,
        'containerOptions' => ['style'=>'overflow: auto'], // only set when $responsive = false
        'toolbar' =>  [
            Html::a(
                ' <i class="fa fa-remove"></i> Удалить Все',
                ['delete-all'],
                [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'method' => 'post',
                    ],
                ]
            ),
            '{export}',
        ],

        'pjax' => true,
        'bordered' => true,
        'striped' => false,
        'condensed' => false,
        'responsive' => true,
        'hover' => true,
        'panel' => [
            'type' => GridView::TYPE_SUCCESS,
            'options'=>['style'=>'width:100%'],
        ],
        'panelBeforeTemplate' => $panelBeforeTemplate,
    ]);

    ?>

</div>
 