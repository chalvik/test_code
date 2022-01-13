<?php
/**
 * Created by PhpStorm.
 * User: Chernogor Alexey
 * Date: 08.12.17
 * Time: 14:53
 */
?>

<div class="row">
    <!-- CARGO ROW -->
    <div class="col-md-2">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Загрузка рейса</h3>

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
                        <?php if (count($model->loadDetails) !== 0): ?>
                            <thead>
                            <tr>
                                <th>Type</th>
                                <th>Unit</th>
                                <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($model->loadDetails as $cargo): ?>
                                <tr>
                                    <td><?= $cargo->type ?></td>
                                    <td><?= $cargo->unit ?></td>
                                    <td><?= $cargo->amount ?></td>
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
    <!-- TABLE: LATEST ORDERS -->
    <div class="col-md-2">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Загрузка отсеков</h3>

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
                        <?php if (count($model->loadCompartment) !== 0): ?>
                            <thead>
                            <tr>
                                <th>Designator</th>
                                <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($model->loadCompartment as $cargo): ?>
                                <tr>
                                    <td><?= $cargo->designator ?></td>
                                    <td><?= $cargo->amount ?></td>
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
            <!-- /.box-body -->

            <!-- /.box-footer -->
        </div>

    </div>
    <!-- TABLE: LATEST ORDERS -->
    <div class="col-md-4">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Ffm</h3>

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
                        <?php if (count($model->ffm) !== 0): ?>
                            <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Airport Load ID</th>
                                <th>Airport Export ID</th>
                                <th>Description</th>
                                <th>Pieces</th>
                                <th>Weight</th>
                                <th>Imp</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($model->ffm as $cargo): ?>
                                <tr>
                                    <td><?= (isset($cargo->invoice)?:"--"); ?></td>
                                    <td><?= (isset($cargo->airportFrom->iata)?:"--"); ?></td>
                                    <td><?= (isset($cargo->airportTo->iata)?:"--"); ?></td>
                                    <td><?= (isset($cargo->description)?:"--"); ?></td>
                                    <td><?= (isset($cargo->pieces)?:"--"); ?></td>
                                    <td><?= (isset($cargo->weight)?:"--"); ?></td>
                                    <td><?= (isset($cargo->imp)?:"--"); ?></td>
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

            </div>


        </div>

    </div>


    <div class="col-md-4">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Загрузка рейса (почта)</h3>

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
                        <?php if (count($model->mails) !== 0): ?>

                            <thead>
                            <tr>
                                <th>Invoice Number</th>
                                <th>Airport From ID</th>
                                <th>Airport To ID</th>
                                <th>Rec Date</th>
                                <th>Weight</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($model->mails as $cargo): ?>
                                <tr>

                                    <td><?= $cargo->invoice_number ?></td>
                                    <td><?= $cargo->airportFrom->iata ?></td>
                                    <td><?= $cargo->airportTo->iata ?></td>
                                    <td><?= $cargo->rec_date ?></td>
                                    <td><?= $cargo->weight ?></td>
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
            </div>

        </div>

    </div>
</div>
