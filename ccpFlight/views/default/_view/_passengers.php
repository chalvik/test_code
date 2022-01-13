<?php

/* @var $this \yii\web\View */

use common\modules\EdbPassenger\models\helpers\EdbPassengerExtractor;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $model \common\modules\ccpFlight\models\Flight|\yii\db\ActiveRecord */

EdbPassengerExtractor::infantCodePreload($model->passengers);
$dataProvider = new \yii\data\ActiveDataProvider(['query' => $model->getPassengers(), 'pagination' => false]);

echo GridView::widget([
    'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'ID_',
//            'FQT_NAME:ntext',
//            'LETTER:ntext',
//            'BAGGAGEWEGHT',
//            'CONTACT_INFO:ntext',
//            'TMSTMP',
        'FIRSTNAME:ntext',
        'SURNAME:ntext',
        //'SEAT_OLD:ntext',
        //'LOUNGE',
        //'CARRIER:ntext',
        //'BOARDING_DATE_TIME',
        //'STD_LOCAL_DATE',
        //'STATUS_FLOWN:ntext',
        //'TYPE_REGRADE:ntext',
//            'CLASS_BOOKING:ntext',
        //'EMD:ntext',
        //          'FQT_STATUS_ALLIANCE:ntext',
        'BOOKING:ntext',
        //'PT_NUMBER_TATTOO',
        'SEAT:ntext',
        //'VIP_NAME:ntext',
        //'ACC_REASON:ntext',
        //'BOARDING_NUMBER',
        //'TKT_COUPON',
        'CLASS_SERVICES:ntext',
        //'SHORTLEG:ntext',
        //'SSR_INFO:ntext',
        //'GATE_CHKIN:ntext',
        //'PAX_TYPE:ntext',
        //'CATEGORIES:ntext',
        //'DOCS_SRC:ntext',
        //'STD_UTC',
        //'NAME:ntext',
        'ROWHASH:ntext',
        [
            'attribute' => 'codes',
            'value' => function ($model) {
                return implode(",", array_map(function ($item) {
                    return $item->code;
                }, $model->codes));
            }
        ],
        //'TYPETKT:ntext',
        //'LOCATOR_DATE_TIME',
        //'NATIONALITIES:ntext',
        //   'FLIGHT_ID',
        //'DOCNUMBER:ntext',
        //'TYPEINFO:ntext',
        //'BAGGAGECNT',
        //'NUMBER_PAX_GROUP',
        //'ARR:ntext',
        //'SITA_BOOKING:ntext',
        //'STD_AIRPORT',
        //'PATRONYMIC:ntext',
        //'CLASS_BOOKING_ORIG:ntext',
        //'CLASS_REGRADE:ntext',
        'BIRTHDATE',

        //'STD_UTC_DATE',
        //'SA:ntext',
        //'IOFL:ntext',
        // 'FLT',
        //'FQT_AK:ntext',
        //'SA_CODE:ntext',
        'PASSENGER_STATUS:ntext',
        //'GENDER:ntext',
        //'IOFL_JSON:ntext',
        //'WEB_CHKIN:ntext',
        //'VIP_POSITION:ntext',
        //'TYPE_GROUP:ntext',

        //'LOCATOR_PNR:ntext',
        //'FQT_STATUS:ntext',
        //'CHKIN_DATE_TIME',
        //'ST_NUMBER_TATTOO',
        //'FQT:ntext',
        //'CHKIN_AGENT:ntext',
        //'DEP:ntext',
        //'GROUP_NAME:ntext',
        //'FQT_TYPE:ntext',
        'TICKET:ntext',
        //'CANCEL_DATE_TIME',
        //'UCI:ntext',
        //'TITLE:ntext',
        //'PK:ntext',
        //'BAGGAGELABEL:ntext',
        //'PROCESSED_TIMESTAMP',
        //'SCN',

        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view}',
            'urlCreator' => function ($action, $model, $key, $index)
            {
                $params = is_array($key) ? $key : ['id' => (string) $key];
                $params[0] = '/admin/passenger/default/' . $action;
                 return Url::toRoute($params);
            }
        ],
    ],
]);
