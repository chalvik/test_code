<?php
/**
 * Created by PhpStorm.
 * User: Chernogor Alexey
 * Date: 08.12.17
 * Time: 13:10
 */

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\modules\ccpFlight\models\FlightTypes;

?>

<div class="flight-view">
    <h1><?=Html::encode($this->title) ?></h1>

    <p>
        <?php echo Html::a(
            Yii::t('flight', 'Update'),
            [
                'update', 'id' => $model->id
            ],
            [
                'class' => 'btn btn-primary'
            ]
        ); ?>
        <?php echo Html::a(
            Yii::t('flight', 'Delete'),
            ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('flight', 'Are you sure you want to delete this item?'),
                'method' => 'post',
                ],
            ]
        ); ?>
    </p>

    <?php echo DetailView::widget(
        [
        'model' => $model,
        'attributes' => [
            'id',
            'day',
            'flt',
            'fltDes',
            'os',
            'carrier',
            'aircraft_id',
            'dep_airport_id',
            'arr_airport_id',
            [
                'attribute' => 'flightInterType',
                'label' => 'Тип рейса',
                'type' => 'raw',
                'value' => function ($model) {
                    if ($model->flightInterType !== null) {
                        return FlightTypes::list()[$model->flightInterType] ?? null;
                    }
                }
            ],
            'flightInterType',
            'origin_std_date',
            'std',
            'sta',
            'etd',
            'eta',
            'blof',
            'blon',
            'tkof',
            'tdown',
            'arr_gate',
            'dep_gate',
            'dep_stand',
            'arr_stand',
            'interval',
        //    'arr_weather',
        //    'dep_weather',
            'deleted:boolean',
            'deleted_at',
            'deleted_user_id',
            'canceled',
            'updated_user_id',
            'created_at',
            'updated_at',
            'last_updated_at',
            'changed_at',
            ],
        ]
    ); ?>

</div>
