<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\modules\ccpEmployee\models\Employee;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\ccpEmployee\models\search\EmployeeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('employee', 'Employees');
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
<div class="employee-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

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
            'id',
            [
                'format' => 'html',
                'label' => 'image',
                'value' => function ($model) {
                    if ($model->file_id) {
                        return Html::img(['get','id'=>$model->file_id], ['class' => 'employee_list']);
                    } else {
                        return Html::img('/img/logo.png', ['class' => 'employee_list']);
                    }
                },
            ],
            'roster_id',
            'fio_eng',
            'fio_rus',
            'port_base',
            'quals_list',
            [
                'attribute' => 'crewcatidx',
                'format' => 'raw',
                'value' => function ($model) {
                    $crewcatidx = \common\components\PSqlDecoder::decodeArray($model->crewcatidx);
                    $crewcatidx = implode(", ", $crewcatidx);
                    return $crewcatidx;
                }
            ],
            'last_updated_at',
            [
                'attribute' => 'type',
                'value' => function ($model) {
                    return isset(Employee::$list_types[$model->type])?Employee::$list_types[$model->type]:"error";
                },
                'filter' => Employee::$list_types
            ],
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
                '<i class="glyphicon glyphicon-plus-sign"></i> '.Yii::t('scheduler', 'Create'),
                ['create'],
                ['class' => 'btn btn-success']
            ).
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

