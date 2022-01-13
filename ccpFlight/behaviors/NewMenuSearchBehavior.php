<?php

namespace common\modules\ccpFlight\behaviors;

use common\models\base\BaseActiveRecord;
use common\modules\ccpFlight\models\Flight;
use common\modules\food\models\ActiveQuery\MenuSettingActiveQuery;
use common\modules\food\models\Menu;
use common\modules\food\models\MenuSettings;
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
final class NewMenuSearchBehavior extends Behavior
{

    /**
     * @var MenuSettingActiveQuery search menu settings
     */
    private $query;

    public $log = [];

    private $filteringMethods = [
        'filterArrivalAirport',
        'filterDepartureAirport',
        'filterFlt',
        'filterDirections',
        'filterAircraftIcao'
    ];

    public $menuList = [];


    /**
     * @var Flight|BaseActiveRecord the owner of this behavior
     */
    public $owner;

    /**
     * Пытается найти меню по установкам меню на рейсы
     * при удачном поиске возвращает объект меню, иначе null
     *
     * Tryes to find available menu by settings
     * returns boolean if menu not found
     * @param MenuSettings $menuSettings
     * @return bool
     */


    public function isInverted(MenuSettings $menuSettings)
    {
        return $menuSettings->invert ? true : false;
    }


    public function filterArrivalAirport(MenuSettings $menuSetting)
    {
        if ($menuSetting->arr_airports) {
            if (is_array($menuSetting->arr_airports)) $arrivalAirports = $menuSetting->arr_airports;
            else  $arrivalAirports = $menuSetting->arr_airports->getValue();

            if (count($arrivalAirports) == 0) return true;

            /** @var Flight $flight */
            $flight = $this->owner;

            if (in_array($flight->arr_airport_id, $arrivalAirports)) {
                if (!$this->isInverted($menuSetting)) {
                    return true;
                } else return false;
            }

        } else return true;

    }

    public function filterDepartureAirport(MenuSettings $menuSetting)
    {
        if ($menuSetting->dep_airports) {
            if (is_array($menuSetting->dep_airports)) $airports = $menuSetting->dep_airports;
            else  $airports = $menuSetting->dep_airports->getValue();

            if (count($airports) == 0) return true;

            /** @var Flight $flight */
            $flight = $this->owner;

            if (in_array($flight->dep_airport_id, $airports)) {
                if (!$this->isInverted($menuSetting)) {
                    return true;
                } else return false;
            }
            return false;
        } else return true;

    }

    public function filterFlt(MenuSettings $menuSetting)
    {

        if ($menuSetting->flight_flts) {

            if (is_array($menuSetting->flight_flts)) $flight_flts = $menuSetting->flight_flts;
            else  $flight_flts = $menuSetting->flight_flts->getValue();

            if (count($flight_flts) == 0) return true;

            /** @var Flight $flight */
            $flight = $this->owner;

            if (in_array($flight->flt, $flight_flts)) {
                if (!$this->isInverted($menuSetting)) {
                    return true;
                } else return false;
            }
            return false;
        } else return true;

    }

    public function filterAircraftIcao(MenuSettings $menuSetting)
    {

        if ($menuSetting->aircraft_icao) {

            if (is_array($menuSetting->aircraft_icao)) $aircraft_icao = $menuSetting->aircraft_icao;
            else {
                $aircraft_icao = $menuSetting->aircraft_icao->getValue();
            }

            if (count($aircraft_icao) == 0) return true;
            /** @var Flight $flight */
            $flight = $this->owner;

            if (in_array($flight->aircraft->icao_id, $aircraft_icao)) {
                if (!$this->isInverted($menuSetting)) {
                    return true;
                } else return false;
            }


        } else return true;


    }

    /**
     * @param MenuSettings $menuSetting
     * @return bool
     */
    public function filterDirections(MenuSettings $menuSetting)
    {

        if ($menuSetting->directionsRelation) {
            /** @var MenuSettingsDirection $direction */
            foreach ($menuSetting->directionsRelation as $direction) {
                if (($direction->arr_airport == $this->owner->arr_airport_id) && ($direction->dep_airport == $this->owner->dep_airport_id)) {
                    return true;
                }
            }
            return false;
        } else return true;

    }


    /**
     * @return Menu|null
     */

    public function getAvailableMenu()
    {
        $this->query = MenuSettings::find()->published();

        try {
            $this->setMainFilters();
            $this->filteringMenuSettings();
            if ($menuList = $this->menuList) {

                $menu_id = array_shift($menuList);
                $this->owner->menu_id = $menu_id;
                return Menu::findOne($menu_id);
            }

        } catch (NewMenuSearchBehaviorException $e) {
            //@todo log $e
        }

        return null;
    }

    public function addToMenuList(int $menu_id)
    {
        $this->menuList[] = $menu_id;
        $this->menuList = array_unique($this->menuList);
    }


    /**
     * filtering all methods defined in $this->filteringMethods
     * @throws NewMenuSearchBehaviorException
     */

    public function filteringMenuSettings()
    {
        if ($this->query) {
            if ($menuSettings = $this->query->all()) {
                /** @var MenuSettings[] $menuSettings */
                foreach ($menuSettings as $menuSetting) {
                    $success = true;
                    foreach ($this->filteringMethods as $method) {
                        if ($success = ($boolean = $this->{$method}($menuSetting)) && $success) {
                            $this->log[$method] = $boolean;
                        } else {
                            $this->log[$method] = $boolean;
                            continue;
                        }
                    }

                    if ($success) $this->addToMenuList($menuSetting->menu_id);
                }
            } else {
                throw new NewMenuSearchBehaviorException('No menu settings found for these date settings.');
            }
        }
    }


    /**
     * @return array
     */
    public function getMenuList(): array
    {
        return $this->menuList;
    }

    private function setMainFilters()
    {
        $stdTimestamp = $this->owner->stdAirport;

        $isEven = (bool)(gmdate("W", $stdTimestamp) % 2);
        $evenValues = $isEven ? [0, 2] : [0, 1];
        $this->query->andWhere(['is_even' => $evenValues]);
        // comment as DEPRECATED
        //  $this->query->andWhere(['carrier' => [$this->owner->carrier, Carrier::CARRIER_ALL]]);
        $stdDate = gmdate('Y-m-d', $stdTimestamp);
        $this->query->andWhere(['>=', 'end_date', $stdDate])
            ->andWhere(['<=', 'start_date', $stdDate]);
        $this->query->joinWith('directionsRelation as dir');
    }
}
