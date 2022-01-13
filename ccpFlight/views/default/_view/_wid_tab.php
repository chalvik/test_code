<?php
/**
 * Created by PhpStorm.
 * User: Chernogor Alexey
 * Date: 08.12.17
 * Time: 15:05
 */

use yii\helpers\Html;
use yii\helpers\Url;
use backend\widgets\WMap\WMap;

?>


<div class="row">
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-user"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Пассажиры на рейсе</span>
                <span class="info-box-number"><?= $model->getPassengers()->count() ?>
                    <small> чел</small></span>
                <?= Html::a('Подробнее...', Url::to(['/admin/passenger/default/index', 'FlightPassengerSearch[flight_id]' => $model->id]), ['class' => 'btn btn-info __grey']) ?>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-arrows-alt"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Трансферники</span>
                <span class="info-box-number"><?= $model->getTransfer()->count() ?>
                    <small> чел</small></span>
                <?= Html::a('Подробнее...', Url::to(['/admin/passenger/transfer/index', 'FlightPassengerTransferSearch[flight_id]' => $model->id]), ['class' => 'btn btn-info __grey']) ?>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->

    <!-- fix for small devices only -->
    <div class="clearfix visible-sm-block"></div>

    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-balance-scale"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Плечи рейса</span>
                <span class="info-box-number"><?= $model->getLegs()->count() ?></span>
                <?= Html::a('Подробнее...', Url::to(['/admin/flight/leg/index', 'FlightLegSearch[flight_id]' => $model->id]), ['class' => 'btn btn-info __grey']) ?>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="fa fa-book"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Отчеты по рейсу</span>
                <span class="info-box-number"><?= $model->getReports()->count() ?></span>
                <?= Html::a('Подробнее...', Url::to(['/admin/report/default/index', 'ReportSearch[flight_id]' => $model->id]), ['class' => 'btn btn-info __grey']) ?>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
</div>


<div class="row">
    <!-- Left col -->
    <div class="col-md-8">
        <!-- MAP & BOX PANE -->
        <?= WMap::widget(['coords' => $coords]) ?>
    </div>

    <div class="col-md-4">


        <?=Html::img('/img/aircraft.jpg', ['style'=>'width:100%']); ?>

        <div class="info-box bg-green">
            <span class="info-box-icon"><i class="fa fa-fighter-jet fa-flip-horizontal"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Departure: <?= $model->depAirport->city ?></span>
                <span class="info-box-number"><?= $model->depAirport->iata ?>
                    Gate: <?= $model->dep_gate ?></span>
                <span class="info-box-text">UTC-STD: <?= $model->std ?> </span>
                <span class="info-box-text">loc-STD: <?= gmdate("Y-m-d- H:i:s",$model->stdAirport) ?> </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="fa fa-fighter-jet"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Arrival: <?= $model->arrAirport->city ?> </span>
                <span class="info-box-number"><?= $model->arrAirport->iata ?>
                    Gate: <?= $model->arr_gate ?></span>
                <span class="info-box-text">UTC-STA: <?= $model->sta ?> </span>
                <span class="info-box-text">loc-STA: <?= gmdate("Y-m-d- H:i:s",$model->staAirport) ?> </span>

            </div>

        </div>

        <div class="progress-group">
            <?php if (is_null($model->blof)): ?>
                <span class="progress-text">Ожидает</span>
            <?php elseif ($current > 100): ?>
                <span class="progress-text">Полет окончен</span>
            <?php else: ?>
                <span class="progress-text">В полете</span>
                <div class="progress sm">
                    <div class="progress-bar progress-bar-aqua" style="width: <?= $current ?>%"></div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- /.col -->
</div>


<div class="row">


    <!-- TABLE: LATEST ORDERS -->
    <div class="col-md-4">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Notifications</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                            class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                            class="fa fa-times"></i></button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table no-margin">
                        <?php if (count($model->notifications) !== 0): ?>
                            <thead>
                            <tr>
                                <th>Owner ID</th>
                                <th>Content</th>
                                <th>Status</th>
                                <th>Created at</th>
                                <th>Updated at</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($model->notifications as $notify): ?>
                                <tr>
                                    <td><?= $notify->owner_id ?></td>
                                    <td><?= $notify->content ?></td>
                                    <td><?= $notify->status ?></td>
                                    <td><?= $notify->created_at ?></td>
                                    <td><?= $notify->updated_at ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        <?php else: ?>
                            <thead>
                            <tr>
                                Данных нет
                            </tr>
                            </thead>
                        <?php endif; ?>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
        </div>

    </div>


    <div class="col-md-4">

        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Экипаж на рейсе</h3>

                <div class="box-tools pull-right">
                    <span class="label label-danger"><?= count($model->crew); ?></span>
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                            class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                            class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
                <ul class="users-list clearfix">
                    <?php foreach ($model->crew as $empl) :?>
                        <li>
                            <?php if ($empl->employee && $empl->employee->file_id) :?>
                                <?= Html::img(
                                    [
                                        '/admin/employee/default/get',
                                        'id' => $empl->employee->file_id,
                                        'width' => 100,
                                        'height' => 100
                                    ],
                                    [
                                        'style' => ['width' => '100px']
                                    ]
                                ); ?>
                            <?php else: ?>
                                <img src="/img/no-image.png" style="width:100px">
                            <?php endif; ?>
                            <?= Html::a($empl->employee->name, Url::to(['/admin/employee/default/view', 'id' => $empl->employee->id])) ?>
                            <span class="users-list-date"><?= $empl->pos_code ?></span>
                        </li>

                    <?php endforeach; ?>

                </ul>
                <!-- /.users-list -->
            </div>

        </div>
    </div>

</div>





<div class="row">
    <!-- Aircraft ROW -->
    <div class="col-md-4">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Aircraft ACO</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                            class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                            class="fa fa-times"></i></button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table no-margin">
                        <?php if (!is_null($model->aircraft->aco)): ?>

                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>File ID</th>
                                <th>Updated At</th>

                            </tr>
                            </thead>
                            <tbody>

                            <tr>
                                <td><?= $model->aircraft->aco->name ?></td>
                                <td><?= $model->aircraft->aco->file_id ?></td>
                                <td><?= $model->aircraft->aco->updated_at ?></td>
                            </tr>

                            </tbody>

                        <?php else: ?>
                            <thead>
                            <tr>
                                Данных нет
                            </tr>
                            </thead>
                        <?php endif; ?>

                    </table>
                </div>

            </div>


        </div>

    </div>


    <div class="col-md-4">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Aircraft Defects</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                            class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                            class="fa fa-times"></i></button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table no-margin">
                        <?php if (!is_null($model->aircraft->defect)): ?>

                            <thead>
                            <tr>


                                <th>Aircraft ID</th>
                                <th>Oil ID</th>
                                <th>Mel ID</th>
                                <th>Updated At</th>

                            </tr>
                            </thead>
                            <tbody>

                            <tr>

                                <td><?= $model->aircraft->defect->aircraft_id ?></td>

                                <td><?= $model->aircraft->defect->oil_id ?></td>
                                <td><?= $model->aircraft->defect->mel_id ?></td>
                                <td><?= $model->aircraft->defect->updated_at ?></td>

                            </tr>

                            </tbody>

                        <?php else: ?>
                            <thead>
                            <tr>
                                Данных нет
                            </tr>
                            </thead>
                        <?php endif; ?>

                    </table>

                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->

            <!-- /.box-footer -->
        </div>

    </div>
</div>