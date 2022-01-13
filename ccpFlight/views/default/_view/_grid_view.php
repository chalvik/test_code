<?php

use yii\helpers\Html;

$roster_id = Yii::$app->request->get("FlightSearch");
$roster_id = $roster_id['crew_roster_id'];

?>

<div class="detail_flight">

    <div class="row">
        <div class="col-md-2">
            <h3> Фактическое время  </h3>

            <?php $class = (!$model->tdown)?'warning':'success'?>
            <div class="callout callout-<?=$class?>">
                <h4>Касание земли</h4>
                <p><?=$model->tdown?></p>
            </div>
            <?php $class = (!$model->tkof)?'warning':'success'?>
            <div class="callout callout-<?=$class?>">
                <h4>Отрыв от земли</h4>
                <p><?=$model->tkof?></p>
            </div>
            <?php $class = (!$model->blof)?'warning':'success'?>
            <div class="callout callout-<?=$class?>">
                <h4>Снятие с тормоза</h4>
                <p><?=$model->blof?></p>
            </div>
            <?php $class = (!$model->blon)?'warning':'success'?>
            <div class="callout callout-<?=$class?>">
                <h4>Постановка на тормоз</h4>
                <p><?=$model->blon?></p>
            </div>

        </div>

        <div class="col-md-2">
        </div>

        <div class="col-md-2">
        </div>


        <div class="col-md-3">
            <h3> Время обновлений  </h3>
            <?php $class = (!$model->last_updated_at)?'warning':'success'?>
            <div class="callout callout-<?=$class?>">
                <h4>last_updated_at</h4>
                <p><?=$model->last_updated_at?></p>
            </div>
            <?php $class = (!$model->changed_at)?'warning':'success'?>
            <div class="callout callout-<?=$class?>">
                <h4>changed_at</h4>
                <p> <?=$model->changed_at?></p>
            </div>
            <?php $class = (!$model->estimated)?'warning':'success'?>
            <div class="callout callout-<?=$class?>">
                <h4>estimated</h4>
                <p><?=$model->estimated?></p>
            </div>

        </div>

        <div class="col-md-3">
            <?php
            $count = count($model->passengers);
            '<div><i class="fa fa-users text-green fa-2x"></i> '.$count.'</div>';
            ?>
            <h3> Экипаж </h3>
            <?php foreach ($model->crew as $crew) :?>
                    <div style="min-width: 200px; margin-bottom: 5px;">
                        <div  style="border-bottom: 1px solid gray;">
                            <?php if ($crew->roster_id == $roster_id) : ?>
                            <div style="width:50px;" >
                                <?php  echo Html::img(
                                    [
                                        '/admin/employee/default/get','id'=>$crew->employee->file_id
                                    ],
                                    [
                                        'style' => "width:50px"
                                    ]
                                );  ?>
                            </div>
                            <?php endif; ?>

                            <div> <?php echo $crew->employee->fio_eng?> </div>
                            <div>
                                <?php if ($crew->roster_id == $roster_id) : ?>
                                    <b><?php echo $crew->roster_id?></b>
                                <?php else : ?>
                                    <?php echo $crew->roster_id?>
                                <?php endif; ?>
                                (<?php echo $crew->pos_code?>)
                            </div>
                        </div>
                    </div>

            <?php endforeach; ?>
        </div>
    </div>

    <div class="row">
        <?=$this->render('_cargo_tab', ['model'=>$model]); ?>
    </div>

</div>
