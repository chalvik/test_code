<?php

use common\modules\ccpFlight\models\FlightLegCrew;
use yii\helpers\Html;

?>

<?php foreach ($model->crew as $crew) :?>
    <?php /** @var FlightLegCrew $crew */?>
    <?php if ($crew->roster_id == $roster_id) : ?>
    <div style="margin-bottom: 5px;">
        <div  style="border-bottom: 1px solid gray;">
            <div >
                <span> найден: </span>
                <?php echo Html::img(
                    [
                        '/admin/employee/default/get','id'=>$crew->employee->file_id
                    ],
                    [
                        'style' => "width:50px"
                    ]
                ); ?>
            </div>
            <div> <?php // echo $crew->employee->fio_eng?> </div>
                <div style="font-size: 12px;">
                    <b><?php echo $crew->roster_id?></b>(<?php echo $crew->pos_code?>)
                </div>
        </div>
    </div>
    <?php endif; ?>
<?php endforeach; ?>

<?php echo(Html::a(
    "<i class='fa fa-users fa-2x'></i> ".count($model->crew),
    ['/admin/flight/leg-crew/index', 'FlightLegCrewSearch[flight_id]' => $model->id])
);

echo '<br><br>'.Html::tag('button','update',['id' => 'force_update_button','data' => ['flight_id' => $model->id],'class' => 'btn btn-success']);

/** @var \common\modules\ccpFlight\models\Flight $model */
if ($model->puCrew) {
    echo '<br><br>'.\yii\helpers\ArrayHelper::getValue($model,'puCrew.roster_id');
}
?>

