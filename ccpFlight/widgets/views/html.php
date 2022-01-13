<?php

/** @var \common\modules\ccpFlight\models\renderers\FlightBriefingRenderer $model */

?>

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
        <td width="40%">LED - Санкт-Петербург<br> 03:15 (UTC +3:00)<br><small><small>Пятница,16 августа</small></small>
        </td>
        <td width="20%">
            <div style="font-size: larger">GH-319</div>
            <small><small> разница во времени:-1 час</small></small></td>
        <td width="40%">LED - Санкт-Петербург<br> 03:15 (UTC +3:00)<br><small><small>Пятница,16 августа</small></small>
        </td>
    </tr>
    </tbody>
</table>
<br>

<div style="font-size: 17px; width: 70%; font-weight: bold; text-align: left">Летный состав</div>

<table class="small">
    <tbody>
    <tr>
        <td style="min-width: 50px">КВС</td>
        <td>Петров Иван Сергеевич</td>
    </tr>

    <tr>
        <td style="min-width: 50px">ВП</td>
        <td>Петров Иван Сергеевич</td>
    </tr>
    </tbody>
</table>

<table class="small" width="100%" style="margin-right: 150px">
    <tbody>
    <tr>
        <td colspan="2" width="450px">
            <div style="font-size: 17px; width: 70%; font-weight: bold; text-align: left">Кабинный экипаж</div>
        </td>
        <td>Тех.номер</td>
    </tr>
    <tr>
        <td width="50px">PU</td>
        <td>Петров Иван Сергеевич</td>
        <td>1H</td>
    </tr>

    <tr>
        <td width="50px">PU</td>
        <td>Петров Иван Сергеевич</td>
        <td>1H</td>
    </tr>
    </tbody>
</table>


<table class="small" width="100%">
    <tbody>
    <tr>
        <td colspan="3"><b>ВС</b>:A319(VQ-BCI) 89 мест Эконом/Бизнес</td>
    </tr>
    <tr>
        <td>Зарегистрировано: <b>89</b></td>
        <td>Эконом: <b>80</b></td>
        <td>Бизнес: <b>10</b></td>

    </tr>
    </tbody>
</table>

<table class="small" width="100%" style="margin-top: 10px">
    <tbody>
    <tr>
        <td colspan="3"><b>Спец.пассажиры</b></td>
    </tr>

    <tr>
        <td>U</td>
        <td>РОРОРО</td>
        <td>Петров Николай Серегеевич</td>
        <td>3F</td>

    </tr>
    <tr>
        <td>U</td>
        <td>РОРОРО</td>
        <td>Петров Николай Серегеевич</td>
        <td>3F</td>

    </tr>
    </tbody>
</table>

<table class="small" width="100%" style="margin-top: 10px">
    <tbody>
    <tr>
        <td colspan="3"><b>Поздравления на борту</b></td>
    </tr>

    <tr>
        <td>ФИО:</td>
        <td>Описание</td>
        <td>Место</td>
    </tr>
    <tr>
        <td>Петров Николай Петрович</td>
        <td>Поздравить с юбилеем и вручить подарок на борту</td>
        <td>4F</td>
    </tr>
    <tr>
        <td>Петров Николай Петрович</td>
        <td>Поздравить со свадьбой. И вручить торт</td>
        <td>67G</td>
    </tr>
    </tbody>
</table>

<p style="margin-top: 10px;"><b>Питание: </b> Завтрак, Обед</p>
<h3>Грузы</h3>
<table class="small bordered center" width="100%" style="margin-top: 10px;" >
    <tbody>
    <tr>
        <td colspan="3"><b>Предварительные данные по загрузке (CRG)</b></td>
    </tr>

    <tr>
        <td>Тип:</td>
        <td>Кол-во</td>
        <td>Единица измерения</td>
    </tr>
    <tr>
        <td>Mail</td>
        <td>1.2</td>
        <td>кг</td>
    </tr>
    <tr>
        <td>Cargo</td>
        <td>3.4</td>
        <td>кг</td>
    </tr>

    </tbody>

</table>

<table class="small bordered center" width="100%" style="margin-top: 10px;" >
    <tbody>
    <tr>
        <td colspan="5"><b>Служебная и коммерческая корреспонденция (INF)</b></td>
    </tr>

    <tr>
        <td>Номер счета-фактуры</td>
        <td>Масса</td>
        <td>Дата получения</td>
        <td>Из</td>
        <td>В</td>
    </tr>
    <tr>
        <td>TF8967687686</td>
        <td>0</td>
        <td>19.09.2019</td>
        <td>AAQ</td>
        <td>CEK</td>
    </tr>

    </tbody>
</table>


<div class='page_break'></div>

<table class="small bordered center" width="100%" style="margin-top: 10px;" >
    <tbody>
    <tr>
        <td colspan="7"><b>Специальные и опасные грузы (FFM)</b></td>
    </tr>

    <tr>
        <td>Номер счета-фактуры</td>
        <td>А/П загрузки</td>
        <td>А/П выгрузки</td>
        <td>Описание</td>
        <td>Кол-во мест</td>
        <td>Масса</td>
        <td>IMP</td>
    </tr>
    <tr>
        <td>421-57378787878</td>
        <td>19</td>
        <td>42</td>
        <td>TNP</td>
        <td>73</td>
        <td>213.6</td>
        <td></td>
    </tr>

    </tbody>
</table>

<h3>Особенности рейса:</h3>
<table class="small bordered center" width="100%" style="margin-top: 10px;" >
    <tbody>
    <tr>
        <td>Описание</td>
        <td>Действие</td>
    </tr>

    <tr>
        <td>Прохладительные напитки на ресах с 3.30</td>
        <td>На прямом и обратных рейсах пассажирирам предлагается НП </td>

    </tr> <tr>
        <td>Прохладительные напитки на ресах с 3.30</td>
        <td>На прямом и обратных рейсах пассажирирам предлагается НП </td>

    </tr>
    </tbody>

</table>

<h3>Рейсы совместной эксплуатации:</h3>
<p>
    Рейс является рейсом совместной эксплуатации вместе в amirates. Рейс является рейсом совместной эксплуатации вместе в amirates. Рейс является рейсом совместной эксплуатации вместе в amirates. Рейс является рейсом совместной эксплуатации вместе в amirates. Рейс является рейсом совместной эксплуатации вместе в amirates.
</p>

</body>
</html>
