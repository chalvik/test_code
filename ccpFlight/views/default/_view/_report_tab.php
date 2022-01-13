<?php
/**
 * Created by PhpStorm.
 * User: Chernogor Alexey
 * Date: 08.12.17
 * Time: 14:53
 */

use common\modules\report\models\ReportNote;
use yii\helpers\Html;

?>

<div class="row">
    <!-- Report ROW -->


    <div class="col-md-12">

        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="box-title"> Отчеты </h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="box-group" id="accordion">
                    <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->


                    <?php foreach($model->reports as $report): ?>

                    <div class="panel box box-primary">
                        <div class="box-header with-border">
                            <h4 class="box-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseReport<?=$report->id?>" aria-expanded="false" class="collapsed">
                                    <?=$report->name?> (<?=count($report->notes)?>)
                                </a>
                            </h4>
                        </div>
                        <div id="collapseReport<?=$report->id?>" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                            <div class="box-body">


                                <?php foreach($report->notes as $note): ?>
                                <div class="row" style="border:1px solid black">
                                <div class="col-md-2">

                                    <?php if (count(($note->photos))): // Добавляем картинки  ?>
                                    <?php foreach ($note->photos as $photo):  ?>
                                        <?= Html::img(
                                            [
                                                '/admin/report/note/get',
                                                'id' => $photo->file_id,
                                                'width' => 200,
                                            ],
                                            [
                                                'style' => ['width' => '100px']
                                            ]
                                        ); ?>
                                    <?php endforeach;?>
                                    <?php else: ?>
                                        нет фото
                                    <?php endif;?>

                                </div>
                                <div class="col-md-10">

                                    <?php
                                    $type_fields = $note->GetDataAtrr();
                                    foreach ($type_fields as $key => $value): ?>
                                        <?php
                                            $type_label = \common\modules\report\models\ReportNote::$type_list_label;
                                            $column_title = (isset($type_label[$key]) ? $type_label[$key] : $key);
                                        ?>
                                        <div class="row" >
                                            <div class="col-md-4" style="border:1px solid gray">
                                                <?=$column_title; ?>
                                            </div>
                                            <div class="col-md-8" style="border:1px solid gray">
                                                <?=ReportNote::ValueField($key, $value); ?>
                                            </div>
                                        </div>

                                    <?php endforeach;?>
                                </div>
                                </div>
                                <?php endforeach;?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
        </div>

</div>
