<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use common\modules\scheduler\models\SchedulerTask;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\scheduler\models\search\SchedulerTaskRunSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('scheduler', 'Scheduler Task Runs');
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs('
     $(document).on("click", "#btn-bulk-group a",function(event){
        event.preventDefault();
        var id_gridview = "#gridview-tasks-run";
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
<div class="scheduler-task-run-index">

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
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'task_id',
                'value' => function ($model) {
                    $output = ($model->task)?(Html::a("($model->task_id)-".$model->task->title, ['/admin/scheduler/default/view','id'=>$model->task->id])):"Error";
                    return  $output;
                },
                'format' => 'html',
                'filter' => ArrayHelper::map(SchedulerTask::find()->all(), 'id', 'title'),
            ],
            'started_at',
            'finished_at',
            [
                'label' => 'Time(sec) ',
                'value' => function ($model) use ($searchModel) {
                    return ($model->started_at && $model->finished_at)?(strtotime($model->finished_at)-strtotime($model->started_at)): " - ";
                },
                
            ],
            [
                'attribute' => 'status',
                'value' => function ($model) use ($searchModel) {
                    return $searchModel::$statuses[$model->status];
                },
                'filter' => $searchModel::$statuses,
            ],
            //'status',
            ['class' => 'yii\grid\ActionColumn'],
    ]; ?>

    <?= GridView::widget([
        'id' => 'gridview-tasks-run',
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
