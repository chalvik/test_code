<?php

namespace common\modules\ccpFlight\behaviors;

use common\components\PSqlDecoder;
use common\models\base\BaseActiveRecord;
use common\modules\ccpFlight\models\Carrier;
use common\modules\ccpFlight\models\Flight;
use common\modules\food\models\ActiveQuery\MenuSettingActiveQuery;
use common\modules\food\models\MenuSettings;
use common\modules\food\models\MenuSettingsDirection;
use yii\base\Behavior;

/**
 * Поведение для поиска меню, которое привязано к рейсу
 * по фильтру который ранне выставлен
 *
 * Behavior for identify avaialble flight menu
 * DON'T EXTEND THIS CLASS, PLEASE!
 * Class MenuSearchBehavior
 * @package common\modules\ccpFlight\behaviors
 */
final class MenuSearchBehavior extends Behavior
{

    /**
     * @var MenuSettingActiveQuery search menu settings
     */
    private $query;

    /**
     * @var Flight|BaseActiveRecord the owner of this behavior
     */
    public $owner;

    /**
     * Пытается найти меню по установкам меню на рейсы
     * при удачном поиске возвращает объект меню, иначе null
     *
     * Tryes to find available menu by settings
     * returns NULL if menu not found
     * @return \common\modules\food\models\Menu | NULL Avalable Menu
     */
    public function getAvailableMenu()
    {
        $this->query = MenuSettings::find()->published();
        $this->setOtherFilters();
        $this->setAirportsArrFilter();

        $this->setAirportsDepFilter();

        $this->setFltFilter();
        $this->setDirectionsFilter();

        $this->setAircraftIcaoFilter();

        /* @var $instance MenuSettings */
        $instance = $this->query->one();
        if ($instance && !$instance->invert) {
            return $instance->menu;
        }
//
//        // To Do
//        $this->query = MenuSettings::find()->published();
//        $this->setInvertOtherFilters();
//        $this->setInvertAirportFilters();
//        $this->setInvertFltFilters();
//        $this->setInvertDirectionFltFilters();
//        $this->setInvertAircraftIcaoFilter();
//
//        /* @var $instance MenuSettings */
//        $instance = $this->query->published()->one();
//        if ($instance && $instance->invert) {
//            return $instance->menu;
//        }

        return null;
    }


    /**
     * Фильрация по Icao кодам воздушных судов
     *
     * Set Aircraft ICAO filter
     * set after setOtherFilters
     */
    private function setInvertAircraftIcaoFilter()
    {

        $query = clone $this->query;

        $aicraft_icao = $this->owner->aircraft->icao_id;
        $query->andWhere([
            ["<>", 'aircraft_icao', '{}'],
            ["@>", 'aircraft_icao', PSqlDecoder::bracefy($aicraft_icao)]
        ]);

        if ($query->count()) {
            $this->query = $query;
        }
    }

    /**
     * Фильтрация по установкам номеров рейсов (flt) инвертирунем
     * Set filter for flt
     */
    private function setInvertFltFilters()
    {
        $query = clone $this->query;
        $query->orWhere(["<@", 'flight_flts', PSqlDecoder::bracefy($this->owner->flt)]);

        if ($query->count()) {
            $this->query = $query;
        }
    }

    /**
     * Фильтрация по направлениям (аэропорты вылета прилета) menu_setting_direction
     * Direction flt filter
     */
    private function setInvertDirectionFltFilters()
    {
        $query = clone $this->query;
        $query->joinWith(['directionsRelation AS direction']);
        $query->orWhere(['!=', 'direction.dep_airport', $this->owner->dep_airport_id])
            ->orWhere(['!=', 'direction.arr_airport', $this->owner->arr_airport_id]);
        if ($query->count()) {
            $this->query = $query;
        }
    }

    /**
     * Фильтрация по аэропортам
     * Airport filter
     */
    private function setInvertAirportFilters()
    {
        $query = clone $this->query;
        $query->andWhere(['<@', "dep_airports", PSqlDecoder::bracefy($this->owner->dep_airport_id)])
            ->andWhere(['<@', "arr_airports", PSqlDecoder::bracefy($this->owner->arr_airport_id)]);
        if ($query->count()) {
            $this->query = $query;
        }
    }

    /**
     * Фильтрация по
     * - интервалу
     * - четности нечетности
     * - интервал
     * - завтрак, обед
     * Sets another filters like carrier or interval.
     */
    private function setInvertOtherFilters()
    {
        $stdTimestamp = $this->owner->stdAirport;

        $isEven = (bool)(gmdate("W", $stdTimestamp) % 2);
        $evenValues = !$isEven ? [0, 2] : [0, 1];

        $this->query->andWhere(["!=", 'is_even', $evenValues]);
        $this->query->andWhere(["!=", 'carrier', $this->owner->carrier]);

//        if ($this->owner->interval) {
//            $this->query->andWhere(["!=", 'interval_id', $this->owner->interval]);
//        }
//
//        $hour = (int)gmdate("H", $stdTimestamp);
//        $this->query->andWhere(["!=", 'std_time', (int)($hour > 10 || $hour < 5) + 1]);

        $stdDate = gmdate('Y-m-d', $stdTimestamp);
        $this->query->andWhere(['or', ['<', 'end_date', $stdDate], ['>', 'start_date', $stdDate]]);
    }


    /**
     * Фильрация по Icao кодам воздушных судов
     *
     * Set Aircraft ICAO filter
     * set after setOtherFilters
     */
    private function setAircraftIcaoFilter()
    {
        $query = clone $this->query;
        if (isset($this->owner->aircraft->icao_id)) {
            $aicraft_icao = $this->owner->aircraft->icao_id;
            $query->andWhere([
                'and',
                ["<>", 'aircraft_icao', '{}'],
                ["@>", 'aircraft_icao', PSqlDecoder::bracefy($aicraft_icao)],
            ]);

            if ($query->count()) {
                $this->query = $query;
            } else {
                $this->query->andWhere([
                    'or',
                    ['aircraft_icao' => '{}'],
                ]);
            }
        }
    }

    /**
     * Фильтрация по номеру рейса flt
     * Sets filter by flt field and mutates query
     */
    private function setFltFilter()
    {
        $query = clone $this->query;
        $query->andWhere([
            'and',
            ["<>", 'flight_flts', '{}'],
            ["@>", 'flight_flts', PSqlDecoder::bracefy($this->owner->flt)]
        ]);

        if ($query->count()) {
            $this->query = $query;
        } else {
            $this->query->andWhere(['flight_flts' => '{}']);
        }
    }

    /**
     * Фильтрация по направлением
     * Sets filter by directions. Tryes to find complete matches of arr- and dep- airports
     */
    private function setDirectionsFilter()
    {
        $subQuery = MenuSettingsDirection::find()
            ->where("menu_settings.menu_id = menu_settings_direction.menu_settings_id");

        $query = clone $this->query;
        $query->joinWith(['directionsRelation AS direction']);
        $query->andWhere([
            'and',
            // commented because https://control.iseck.com/issues/10347
            ['direction.arr_airport' => $this->owner->arr_airport_id],
            ['direction.dep_airport' => $this->owner->dep_airport_id]
        ]);

        if ($query->count()) {
            $this->query = $query;
        } else {
            $this->query->andWhere(['not exists', $subQuery]);
        }
    }


    /**
     * Фильтрация по аэропортам
     * Sets filter by airports. Tryes to find arr- and dep- airports don't depend on each other
     */

    private function setAirportsArrFilter()
    {
        $query = clone $this->query;
        $query->andWhere([
            'and',
            ["<>", 'arr_airports', '{}'],
            ["@>", "arr_airports", PSqlDecoder::bracefy($this->owner->arr_airport_id)],
        ]);

        if ($query->count()) {
            $this->query = $query;
        } else {
            $this->query->andWhere(['arr_airports' => '{}']);
        }
    }


    /**
     * Фильтрация по аэропортам
     * Sets filter by airports. Tryes to find arr- and dep- airports don't depend on each other
     */

    private function setAirportsDepFilter()
    {
        $query = clone $this->query;
        $query->andWhere([
            'and',
            ["<>", 'dep_airports', '{}'],
            ["@>", "dep_airports", PSqlDecoder::bracefy($this->owner->dep_airport_id)],
        ]);

        if ($query->count()) {
            $this->query = $query;
        } else {
            $this->query->andWhere(['dep_airports' => '{}']);
        }
    }

    /**
     * Дополнительная фильтрация
     * - четность нечетность
     * - клмпания
     * - интевал
     * - завтрак обед
     * Sets another filters like carrier or interval.
     */
    private function setOtherFilters()
    {
        $stdTimestamp = $this->owner->stdAirport;

        $isEven = (bool)(gmdate("W", $stdTimestamp) % 2);
        $evenValues = $isEven ? [0, 2] : [0, 1];
        $this->query->andWhere(['is_even' => $evenValues]);
        $this->query->andWhere(['carrier' => [$this->owner->carrier, Carrier::CARRIER_ALL]]);

//        if ($this->owner->interval) {
//            $this->query->andWhere([
//                'or',
//                'interval_id = $this->owner->interval',
//                'interval_id = 0'
//            ]);
//        }
//
//        // Поле "время вылета рейса" в фильтрах захардкожено. 1 => '5:00-10:00', 2 => '10:00-5:00'
//        $hour = (int)gmdate("G", $stdTimestamp);
//        $this->query->andWhere([
//            'std_time' => [(int)($hour >= 10 || $hour <= 5) + 1, 3]
//        ]);

        $stdDate = gmdate('Y-m-d', $stdTimestamp);
        $this->query->andWhere(['>=', 'end_date', $stdDate])
            ->andWhere(['<=', 'start_date', $stdDate]);

    }
}
