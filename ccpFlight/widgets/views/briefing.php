<?php

/** @var \common\modules\ccpFlight\models\renderers\FlightBriefingRenderer $model */

use common\modules\additionalInfo\models\AdditionalInfo;
use common\modules\cargo\models\CargoFfm;
use common\modules\cargo\models\FlightLoadDetail;
use common\modules\cargo\models\FlightMail;
use common\modules\ccpFlight\models\FeatureFlight;
use common\modules\EdbPassenger\models\EdbPassengerGift; ?>

<html>

<body style="width:1280px;">
<style>

    @page {
        margin-top: 5mm; /* <any of the usual CSS values for margins> */
        margin-bottom: 5mm; /* <any of the usual CSS values for margins> */
    }

    table {
        border-collapse: collapse;
        margin-top: 5px;
        font-size: 20px;
        width: 100%;
    }

    table.small {
        border-collapse: collapse;
        font-size: 16px;
    }

    table.center td {
        text-align: center;
    }

    table.small td {
        text-align: left;
    }

    .page_break {
        page-break-before: always;
    }

    .bordered td {
        border: dotted 1px black;
    }

    .content-center td {
        text-align: center;
    }

    td {
        border: black dashed 1px;
        text-align: center;
        vertical-align: center;;
    }

    p {
        font-size: 16px;
        padding: 0px;
        margin: 0px;
    }

    br {
        height: 5px;
    }
</style>

<p align="right">report is generated: <?= date('Y-m-d H:i'); ?> </p>
<br>
<table align="left">
    <tbody>
    <tr>
        <td width="40%"><?= $model->value('dep_airport.iata'); ?> - <?= $model->value('dep_airport.city'); ?>
            <br> <?= $model->value('flight.std'); ?>
            (UTC <?= $model->value('dep_airport.time_difference'); ?>
            )<br><small><small><?= $model->value('dep_airport.date'); ?></small></small>
        </td>
        <td width="20%">
            <div style="font-size: larger"><?= $model->value('carrier'); ?>-<?= $model->value('flt'); ?></div>
            <small><small> разница во времени:<?= $model->value('time_difference'); ?> час</small></small></td>
        <td width="40%"><?= $model->value('arr_airport.iata'); ?> - <?= $model->value('arr_airport.city'); ?>
            <br> <?= $model->value('flight.sta'); ?>
            (UTC <?= $model->value('arr_airport.time_difference'); ?>
            )<br><small><small><?= $model->value('arr_airport.date'); ?></small></small>
        </td>
    </tr>
    </tbody>
</table>
<br>

<!--<div style="font-size: 17px; width: 70%; font-weight: bold; text-align: left">Летный состав</div>-->

<table class="small" width="100%" style="margin-right: 150px">
    <tbody>

    <tr>
        <td colspan="2" width="450px">
            <div style="font-size: 17px; width: 70%; font-weight: bold; text-align: left">Летный состав</div>
        </td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td style="width:150px;">Питание </td>
    </tr>

    <?php
    $cabinCrew = array_filter($model->value('crew'), function ($item) {
        return in_array($item['code'], ['FO', 'CP', 'RP']);
    });
    sort($cabinCrew);

    ?>
    <?php foreach ($cabinCrew as $key => $crew): ?>
        <tr>
            <td width="50px"><?= $crew['code'] ?> </td>
            <td><?= $crew['fio']; ?></td>
            <td><?= $crew['number']; ?></td>
            <td style="width:150px;"><?= ($crew['refusalfood']?'Нет':'Есть') ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<table class="small" width="100%" style="margin-right: 150px">
    <tbody>
    <tr>
        <td colspan="2" width="450px">
            <div style="font-size: 17px; width: 70%; font-weight: bold; text-align: left">Кабинный экипаж</div>
        </td>
        <td>Тех.номер</td>
        <td style="width:150px;">Питание </td>
    </tr>

    <?php if ($model->value('crew')
        && ($cabinCrew = array_filter($model->value('crew'), function ($item) {
        return !in_array($item['code'], ['FO', 'CP', 'RP']);
    }))): ?>
        <?php foreach ($cabinCrew as $key => $crew): ?>
            <tr>
                <td width="50px"><?= $crew['code'] ?> </td>
                <td><?= $crew['fio']; ?></td>
                <td><?= $crew['number']; ?></td>
                <td style="width:150px;"><?= ($crew['refusalfood']?'Нет':'Есть') ?></td>
            </tr>
        <?php endforeach ?>
    <?php endif ?>
    </tbody>
</table>


<table class="small" width="100%">
    <tbody>
    <tr>
        <td colspan="3"><b>ВС</b>: <?= $model->value('aircraft.reg'); ?>  <?= $model->value('aircraft.name'); ?>
            <?= $model->value('aircraft.passenger_count'); ?> мест Эконом (<?= $model->value('aircraft.pass_y'); ?>)
            <?php if ($model->value('aircraft.pass_c')) { ?>
            /Бизнес (<?= $model->value('aircraft.pass_c'); ?>)
            <?php } ?>
        </td>
    </tr>
    <tr>
        <td>Зарегистрировано: <b><?= ($model->value('flight.passenger_check_in_y_count') + $model->value('flight.passenger_check_in_c_count'))?:"0" ?></b> из
            <b><?= $model->value('flight.passenger_count'); ?></b></td>
        <td>Эконом: <b><?= $model->value('flight.passenger_check_in_y_count'); ?></b> из
            <b><?= $model->value('flight.passenger_y_count'); ?></b></td>
        <?php if ($model->value('aircraft.pass_c')) { ?>
        <td>Бизнес: <b><?= $model->value('flight.passenger_check_in_c_count'); ?></b> из
            <b><?= $model->value('flight.passenger_c_count'); ?></b></td>
        <?php } ?>
    </tr>
    </tbody>
</table>


<?php if ($model->value('specialCodes')) { ?>
    <table class="small bordered center" width="100%" style="margin-top: 10px">
        <tbody>
        <tr>
            <td colspan="3"><b>Спец.пассажиры</b></td>
        </tr>
        <?php
        foreach ($model->value('specialCodes') as $codeGroup => $codesGroup) {
//          $creepCodes = ['WCHS', 'WCHR'];
          $uniquePassengersInCodes = [];
          foreach ($codesGroup as $singleCode => $singleCodeVal){
//              if(in_array(strtoupper($singleCode), $creepCodes)){
                  foreach ($singleCodeVal as $passengerInCode){
                      $uniquePassengersInCodes[$passengerInCode['uniquePassenger']] = $passengerInCode['uniquePassenger'];
                  }
//              }
          }
          $numderOfUniquePassengersInGroup = count($uniquePassengersInCodes);
        ?>
            <tr>
<!--                <td colspan="2" style="text-align: left;background-color: lightgrey;"><b>--><?//= mb_strtoupper($codeGroup);?><!--("ЭР"--><?//= array_sum(array_map("count", $codesGroup));?><!--)</b></td>-->
                <td colspan="2" style="text-align: left;background-color: lightgrey;"><b><?=mb_strtoupper($codeGroup)?>(<?=$numderOfUniquePassengersInGroup?>)</b></td>
            </tr>
        <?php foreach ($codesGroup  as $code => $passengers) { ?>
            <tr>
                <?php
//                $count = 0;
//                $old_seat= null;
//                foreach ($passengers as $passenger)
//                {
//                    if ($old_seat != $passenger['seat']) {
//                        $count++;
//                        $old_seat = $passenger['seat'];
//                    }
//                 }
                $count = 0;
                $count_seat = 0;
                foreach ($passengers as $passenger){
                  $count++;
                  if($passenger['seat'] != '' AND $passenger['seat'] != null){
                    $count_seat++;
                  }
                  
                }
                 ?>

                <td colspan="2"><b><?= $code;?>(<?=$count;?>)</b></td>
            </tr>
            <?php foreach ($passengers as $passenger) { ?>
                <tr>
                    <td style="text-align: left"><?= $passenger['fullName'] ?></td>
                    <td><?= $passenger['seat'] ?></td>
                </tr>

            <?php } ?>
        <?php } ?>
        <?php } ?>
        </tbody>
    </table>

<?php } ?>

<?php if ($model->value('gifts.free')) { ?>

    <table class="small" width="100%" style="margin-top: 10px">
        <tbody>
        <tr>
            <td colspan="3"><b>Бесплатные поздравления на борту</b></td>
        </tr>
        <tr>
            <td>ФИО:</td>
            <!--   <td>Описание</td>-->
            <td>Место</td>
        </tr>
        <?php /** @var EdbPassengerGift $gift */
        foreach ($model->value('gifts.free') as $gift) { ?>
            <tr>
                <td><?= $gift['fio'] ?></td>
                <!--  <td><? /*= $gift['type']; */ ?></td>-->
                <td><?= $gift['seat']; ?></td>
            </tr>

        <?php } ?>

        </tbody>
    </table>


<?php } ?>


<?php if ($model->value('gifts.payed')) { ?>

    <table class="small" width="100%" style="margin-top: 10px">
        <tbody>
        <tr>
            <td colspan="3"><b>Платные поздравления на борту</b></td>
        </tr>
        <tr>
            <td>ФИО:</td>
            <!--   <td>Описание</td>-->
            <td>Место</td>
        </tr>
        <?php /** @var EdbPassengerGift $gift */
        foreach ($model->value('gifts.payed') as $gift) { ?>
            <tr>
                <td><?= $gift['fio'] ?></td>
                <!-- <td><? /*= $gift['type']; */ ?></td>-->
                <td><?= $gift['seat']; ?></td>
            </tr>

        <?php } ?>

        </tbody>
    </table>


<?php } ?>
<?php if ($model->value('menu.rations')) { ?>
    <p style="margin-top: 10px;"><b>Питание: </b> <?= $model->value('menu.rations'); ?></p>

<?php } ?>

<?php if ($model->value('cargo.details') || $model->value('cargo.mail') || $model->value('cargo.ffm')) { ?>
    <h3>Грузы</h3>
<?php } ?>

<?php /** @var CargoFfm $ffm */
if ($model->value('cargo.details')) { ?>
    <table class="small bordered center" width="100%" style="margin-top: 10px;">
        <tbody>
        <tr>
            <td colspan="3"><b>Предварительные данные по загрузке (CRG)</b></td>
        </tr>

        <tr>
            <td>Тип:</td>
            <td>Кол-во</td>
            <td>Единица измерения</td>
        </tr>
        <?php /** @var FlightLoadDetail $detail */
        foreach ($model->value('cargo.details') as $detail) { ?>
            <tr>
                <td><?= $detail->type; ?></td>
                <td><?= $detail->amount; ?></td>
                <td><?= $detail->unit; ?></td>
            </tr>
        <?php } ?>

        </tbody>

    </table>

<?php } ?>

<?php /** @var CargoFfm $ffm */
if ($model->value('cargo.mail')) { ?>
    <table class="small bordered center" width="100%" style="margin-top: 10px;">
        <tbody>
        <tr>
            <td colspan="5"><b>Служебная и коммерческая корреспонденция (INF)</b></td>
        </tr>

        <?php /** @var FlightMail $mail */
        foreach ($model->value('cargo.mail') as $mail) { ?>
            <tr>
                <td><?= $mail->invoice_number; ?> </td>
                <td><?= $mail->weight; ?> </td>
                <td><?= $mail->rec_date; ?></td>
                <td><?= $mail->airportFrom ? $mail->airportFrom->iata : '' ?></td>
                <td><?= $mail->airportTo ? $mail->airportTo->iata : '' ?></td>
            </tr>

        <?php } ?>

        </tbody>
    </table>

<?php } ?>
<!--<div class='page_break'></div>-->
<?php if ($model->value('cargo.ffm')) { ?>
    <table class="small bordered center" width="100%" style="margin-top: 10px;">
        <tbody>
        <tr>
            <td colspan="7"><b>Специальные и опасные грузы (FFM)</b></td>
        </tr>
        <?php /** @var CargoFfm $ffm */
        foreach ($model->value('cargo.ffm') as $ffm) { ?>
            <tr>
                <td><?= $ffm->invoice; ?></td>
                <td><?= $ffm->airportFrom ? $ffm->airportFrom->iata : '' ?></td>
                <td><?= $ffm->airportTo ? $ffm->airportTo->iata : '' ?></td>
                <td><?= $ffm->description; ?></td>
                <td><?= $ffm->pieces; ?></td>
                <td><?= $ffm->weight; ?></td>
                <td><?= $ffm->imp; ?></td>
            </tr>
        <?php } ?>


        </tbody>
    </table>
<?php } ?>

<h3>Особенности рейса:</h3>

<?php if ($model->value('info')) { ?>
    <table class="small bordered center" width="100%" style="margin-top: 10px;">
        <tbody>
        <tr>
            <td>Описание</td>
            <td>Действие</td>
        </tr>
        <?php /** @var AdditionalInfo $info */
        foreach ($model->value('info') as $info) { ?>
            <tr>
                <td><?= $info->title; ?></td>
                <td><?= $info->text; ?></td>

            </tr>
        <?php } ?>
        </tbody>

    </table>

<?php } else echo "нет"; ?>

<?php if ($model->value('features')) { ?>
    <h3>Рейсы совместной эксплуатации:</h3>
    <?php /** @var FeatureFlight $feature */
    foreach ($model->value('features') as $feature) { ?>
        <p>
            <?= $feature->note; ?>
        </p>
    <?php } ?>
<?php } ?>
</body>
</html>
