<?php
namespace common\modules\ccpFlight\models\renderers;

use Yii;
use common\components\ArrayHelper;
use common\modules\ccpEmployee\models\Employee;
use common\modules\ccpFlight\models\Carrier;
use common\modules\ccpFlight\models\FlightBriefings;
use common\modules\ccpFlight\widgets\BriefingWidget;
use common\modules\EdbPassenger\models\EdbPassenger;
use common\modules\EdbPassenger\models\EdbPassengerCode;
use common\modules\EdbPassenger\models\helpers\EdbPassengerExtractor;

/**
 * Flight Briefing Renderer
 */
class FlightBriefingRenderer
{

    const SPEC_FOOD_GROUP = 22;
    
    protected $briefing;
    protected $flight;
    protected $passengers = [];
    
    public static $spec_food_group = [];

    public $data = [];
    public $crew = [];
    public $body;

    
    public static $daysWeekMap = [
        0 => 'Воскресение',
        1 => 'Понедельник',
        2 => 'Вторник',
        3 => 'Среда',
        4 => 'Четверг',
        5 => 'Пятница',
        6 => 'Суббота',
    ];
    public static $monthsMap = [
        
        1 => 'Января',
        2 => 'Февраля',
        3 => 'Марта',
        4 => 'Апреля',
        5 => 'Мая',
        6 => 'Июня',
        7 => 'Июля',
        8 => 'Августа',
        9 => 'Сентября',
        10 => 'Октября',
        11 => 'Ноября',
        12 => 'Декабря',
    ];

    public static $defaultSkipCodes = [
        'CKIN',
        //'CHD', // regarding CCP-2128  - Отображать CHLD в Брифинге
        'RQST', 'FQTU', 'FQTR', 'FQTS', 'FQTV', 'EMPL_RP', 'EMPL_FO', 'EMPL_CP', 'EMPL_FC', 'EMPL_FI', 'EMPL_PU', 'EMPL_FA'
    ];

    public static $skipIfHasNoOnDependencyCodes = [
        'FQTU' => ['SILVER', 'GOLD', 'PLATINUM', 'RUBY', 'EMERALD', 'SAPPHIRE'],
        'FQTR' => ['SILVER', 'GOLD', 'PLATINUM', 'RUBY', 'EMERALD', 'SAPPHIRE'],
        'FQTS' => ['SILVER', 'GOLD', 'PLATINUM', 'RUBY', 'EMERALD', 'SAPPHIRE'],
        'FQTV' => ['SILVER', 'GOLD', 'PLATINUM', 'RUBY', 'EMERALD', 'SAPPHIRE'],
    ];

    public static $premiumPassengersCodes = [
        'SILV', 'GOLD', 'PLAT', 'RUBY', 'EMERALD', 'SAPPHIRE'
    ];

    public static $premiumPassengersCodesGroups = [
        'RUBY' => 'RUBY/SILVER',
        'SILV' => 'RUBY/SILVER',
        'SAPPHIRE' => 'SAPPHIRE/GOLD',
        'GOLD' => 'SAPPHIRE/GOLD',
        'EMERALD' => 'EMERALD/PLATINUM',
        'PLAT' => 'EMERALD/PLATINUM',
    ];
    
    public function __construct(FlightBriefings $briefing)
    {

        self::$spec_food_group = EdbPassengerCode::find()->andWhere(['group_id' => self::SPEC_FOOD_GROUP])->select('code')->column();
        $this->briefing = $briefing;
        $this->flight = $briefing->flight;
        if ($this->flight) {
            // do not skip infants
            EdbPassengerExtractor::$showINFcode = true;
            EdbPassengerExtractor::infantCodePreload($this->flight->passengers);

//            $this->passengers = EdbPassenger::find()
//                ->where(['FLT' => $this->flight->flt,])
//                ->andWhere(['STD_UTC' => $this->flight->std,])
//                ->andWhere(['NOT IN', 'PASSENGER_STATUS', ['CANCEL', 'DELETED']])
//                ->andWhere(['IS NOT', 'TICKET', null])
//                ->all();
  
            $this->passengers = $this->flight->getPassengers()->all();
  
            $this->composeData();
        }
    }

    public function formatTime($time)
    {
        $hours = floor($time / 60);
        return ($hours > 0 ? "+" . $hours : "-" . $hours) . ":" .
          str_pad($time % 60, 2, '0', STR_PAD_LEFT);
    }

    public function formatDate($date)
    {
        $time = strtotime($date);
        return ArrayHelper::getValue(self::$daysWeekMap, date('w', $time)) . ","
          . date('d', $time) . " " . ArrayHelper::getValue(self::$monthsMap, date('m', $time));
    }
    
    private function additionalInfo()
    {
      $additionalsByFlightQuery = Yii::$app->AdditionalsByFlight->GetAdditionalsByFlight($this->flight->id);
      $this->data['info'] = $additionalsByFlightQuery->all();
    }

    public function composeAirports()
    {
        $flight = $this->flight;

        /** @var Flight $flight */
        $this->data['dep_airport']['iata'] = $flight->depAirport->iata;
        $this->data['dep_airport']['city'] = $flight->depAirport->city;

        $this->data['dep_airport']['time_difference'] = $this->formatTime($flight->depAirport->utc_aims);
        $this->data['dep_airport']['date'] = $this->formatDate($flight->std);

        $this->data['arr_airport']['time_difference'] = $this->formatTime($flight->arrAirport->utc_aims);
        $this->data['arr_airport']['date'] = $this->formatDate($flight->std);

        $this->data['arr_airport']['iata'] = $flight->arrAirport->iata;
        $this->data['arr_airport']['city'] = $flight->arrAirport->city;
        $timeDifference = round(($flight->arrAirport->utc_aims - $flight->depAirport->utc_aims) / 60);

        $this->data['time_difference'] = ($timeDifference > 0) ? "+" . $timeDifference : $timeDifference;
    }

    public function composeAircraft()
    {
        $flight = $this->flight;

        /** @var Flight $flight */

        $this->data['aircraft.name'] = $flight->aircraft->name;
        $this->data['aircraft.reg'] = $flight->aircraft->reg;
        $this->data['aircraft.pass_c'] = $flight->aircraft->pass_c;
        $this->data['aircraft.pass_y'] = $flight->aircraft->pass_y;
        $this->data['aircraft.passenger_count'] = $flight->aircraft->pass_c + $flight->aircraft->pass_y;
    }

    private function composeFlight()
    {
        $flight = $this->flight;

        /** @var Flight $flight */

        // количество пассажиров
        $this->data['flight.passenger_y_count'] = count(array_filter($this->passengers, function (EdbPassenger $model) {
            return !in_array($model->CLASS_BOOKING, ["C", "D", "J", "U", "I"]);
        }));

        $this->data['flight.passenger_check_in_y_count'] = count(array_filter($this->passengers, function (EdbPassenger $model) {
            return (!in_array($model->CLASS_BOOKING, ["C", "D", "J", "U", "I"]))
                && ($model->CHKIN_AGENT
                || $model->GATE_CHKIN || $model->WEB_CHKIN
                || $model->CHKIN_DATE_TIME || $model->PASSENGER_STATUS == 'CHECKIN');
        }));

        $this->data['flight.passenger_c_count'] = count(array_filter($this->passengers, function (EdbPassenger $model) {
            return in_array($model->CLASS_BOOKING, ["C", "D", "J", "U", "I"]);
        }));

        $this->data['flight.passenger_check_in_c_count'] = count(array_filter($this->passengers, function (EdbPassenger $model) {
            return (in_array($model->CLASS_BOOKING, ["C", "D", "J", "U", "I"]))
                && ($model->CHKIN_AGENT
                    || $model->GATE_CHKIN || $model->WEB_CHKIN
                    || $model->CHKIN_DATE_TIME || $model->PASSENGER_STATUS == 'CHECKIN');
        }));

        $this->data['flight.passenger_check_in_count'] = $this->data['flight.passenger_check_in_y_count'] + $this->data['flight.passenger_check_in_c_count'];

        $this->data['flight.passenger_count'] = $this->data['flight.passenger_y_count'] + $this->data['flight.passenger_c_count'];
        $this->data['carrier'] = ArrayHelper::getValue(Carrier::$carrier_list_aims, $flight->carrier);
        $this->data['flt'] = $flight->flt;
        $this->data['std'] = $flight->std;
        $this->data['sta'] = $flight->sta;
    }

    public function composeCrew()
    {
        $flight = $this->flight;

        $this->data['crew'] = [];
        /** @var Flight $flight */

        /** @var FlightLegCrew[] $crew */
        /* ORDER BY THIS ORDER  CP,FO,FI,FC,PU,FA */
        //  ->orderBy("position(pos_code::text in 'CP,FO,FI,FC,PU,FA')")
        $crews = $flight->getCrew()->all();
        if ($crews) {
            /* $order = ['CP', 'FO', 'FI', 'FC', 'PU', 'FA'];
             uksort($crew, function ($a, $b) use ($order) {
                 return $a->pos_code - $b->pos_code;
             });*/
            foreach ($crews as $key => $crew) {
                /** @var $crew FlightLegCrew */
                $this->data['crew'][$key]['fio'] = $crew->employee ? $crew->employee->fio_rus : '';
                $this->data['crew'][$key]['number'] = $crew->pos_leg1 ?: '';
                $this->data['crew'][$key]['code'] = $crew->pos_code;
                $this->data['crew'][$key]['refusalfood'] = $crew->refusalFood ? 1 : 0;
            }

            $order = ['CP', 'FO', 'FI', 'FC', 'PU', 'FA'];

            usort($this->data['crew'], function ($item1, $item2) use ($order) {
                return (array_search($item1['code'], $order) > array_search($item2['code'], $order));
            });
        }
    }
    
    public function setSpecialPassengersData(EdbPassenger $passenger, Array $codes = []){
      $specialPassengersData = [
        'fullName' => isset($passenger) ? $passenger->getFullNameAmadeus() : '',
        'seat' => isset($passenger) ? ltrim($passenger->SEAT, '0') : '',
        'codes' => $codes ? array_unique($codes) : [],
        'uniquePassenger' => isset($passenger) ? $passenger->ID_ . $passenger->NAME . $passenger->SURNAME : uniqid()
      ];
      return $specialPassengersData;
    }

    private function composeCodes()
    {
        /** @var Flight $flight */
        $flight = $this->flight;
        
        // codes
        $allCodes = [];
        if ($this->passengers) {
            //перебор пассажиров
            foreach ($this->passengers as $key => $passenger) {
              
                //возвращает массив с обьектами кодов
                $passenger_codes_dirty = $passenger->codes;
                
                //filter foreign passengers
                //оставляет в массиве с кодами только те коды, чьи группы не соответсвуют кодам иностранных граждан (убирает коды, указывающие на иностранных граждан)
                $passenger_codes = array_filter(
                    $passenger_codes_dirty,
                    function (EdbPassengerCode $model) {
                      return $model->group_id !== EdbPassengerCode::getForeignPassengerGroupId();
                    });
                  
                if($passenger_codes) {
                    $codes = [];
                    //iterate existed codes
                    //$code - 1 обьект с кодом
                    foreach ($passenger_codes as $code) {
                        //собирает массив $codes с конкретными кодами, типа ['GOLD', 'OXYG', 'PETC'], при этом сразу выпиливая заранее устраняемые коды
                        $codes[] = $code->code;
                    }

                    if($codes) {
                        
                        //обработка конкретных кодов
                      
                        //выпиливаем заранее устраняемые коды (default skip codes)
                        $codes = array_diff($codes, self::$defaultSkipCodes);
  
                        // CCP-2084 Спец питание skip if Не отображать в списке спец питания (кроме BBML) инфанта.
                        //выпиливаем из массива с кодами определённые коды для пассажиров с опредлённым типом PAX_TYPE
                        if ($passenger->PAX_TYPE == "INF") $codes = array_diff($codes, ['CHML', 'VGML', 'GFML', 'MOML', 'SFM', 'NLML']);
                        if ($passenger->PAX_TYPE == "CHD") $codes = array_diff($codes, ['BBML']);
                        if ($passenger->PAX_TYPE == "ADT") $codes = array_diff($codes, ['BBML', 'CHML']);
                      
                        //добавляем в главные массивы инфу, если по данному пассажиру подарок оплачен
                        foreach($codes as $codeKey => $codeItem){
                            //  if payed GIFTS
                            if ($codeItem == 'PDTS') {
                                $this->data['gifts']['payed'][] = [
                                   'fio' => $passenger->getFullNameAmadeus(),
                                   'type' => '',
                                   'seat' => ltrim($passenger->SEAT, '0'),
                                ];
                            }
                        }
  
                        // all codes collection Тут собираеются коды всех пассажиров (добавляются новые от каждой итеррации с новым пассажиром)
                        $allCodes = array_merge($allCodes, $codes);
                        
                        //заносим данные в массив со спецпассажирами
                        //$key - ключ массива с обьектами-пассажирами
//                        $this->data['specialPassengers'][$key]['fullName'] = $passenger->getFullNameAmadeus();
//                        $this->data['specialPassengers'][$key]['seat'] = ltrim($passenger->SEAT, '0');
//                        $this->data['specialPassengers'][$key]['codes'] = array_unique($codes);
//                        $this->data['specialPassengers'][$key]['uniquePassenger'] = $passenger->ID_ . $passenger->NAME . $passenger->SURNAME;
                        $this->data['specialPassengers'][$key] = $this->setSpecialPassengersData($passenger, $codes);
                    }
                }
    
                //composeCrewAsPassenger
                // если взрослый и служебный
                if ($passenger->isAdult() && $passenger->isOfficial()) {
                    if ($employee = Employee::find()
                      ->where(['ilike', 'fio_eng', $passenger->getLowerCaseFullName()])
                      ->andWhere(['IS NOT', 'quals_list', NULL])
                      ->andWhere(['<>', 'quals_list', ''])
                      ->one())
                    {
                      // store 'БОРТПРОВОДНИКИ/ПИЛОТЫ'
    //                    $this->data['specialCodes']['БОРТПРОВОДНИКИ/ПИЛОТЫ']['EMPL_'][] = [
    //                      'fullName' => $passenger->getFullNameAmadeus(),
    //                      'seat' => ltrim($passenger->SEAT, '0'),
    //                      'uniquePassenger' => $passenger->ID_ . $passenger->NAME . $passenger->SURNAME,
    //                    ];
                      if(isset($codes) AND $codes) {
                          $this->data['specialCodes']['БОРТПРОВОДНИКИ/ПИЛОТЫ']['EMPL_'][] = $this->setSpecialPassengersData($passenger, $codes);
                      }else{
                          $this->data['specialCodes']['БОРТПРОВОДНИКИ/ПИЛОТЫ']['EMPL_'][] = $this->setSpecialPassengersData($passenger);
                      }
                    }
                }
              
            }
            //>>>перебор пассажиров
        }

        // grouping by codes
        $groupedStatusPassengers = [];
        if ($allCodes) {
            $allCodes = array_unique($allCodes);
            $codeGroupsMaps = EdbPassengerCode::find()
                ->with('group')
                ->indexBy('code')
                ->all();

            //перебор отдельных кодов
            foreach ($allCodes as $code) {

                //CCP-2083 Статусные пассажиры
                if (in_array($code, self::$premiumPassengersCodes)) {   //если код входит в массив кодов статусных пассажиров
                    //например, self::$premiumPassengersCodesGroups['GOLD'] == 'SAPPHIRE/GOLD'
                    //например, тогда $groupedStatusPassengers['SAPPHIRE/GOLD']['GOLD']
                    $groupedStatusPassengers[self::$premiumPassengersCodesGroups[$code]][$code] = array_filter(
                      $this->value('specialPassengers'),   //массив $this->data['specialPassengers'], например $this->data['specialPassengers'][$key]['fullName']
                      function ($model) use ($code) {
                        return isset($model['codes']) && in_array($code, $model['codes']);
                      });
                } else {   //если код НЕ входит в массив кодов статусных пассажиров
                    // else logic
                    /** @var EdbPassengerGroup $group */
                    // grouping by code groups
                    if ($group = $codeGroupsMaps[$code]->group){ //в условие - получаем группу данного кода из таблицы кодов
                      $label = $group->title;
                    }else{
                      $label = 'Другие';
                    }
                    //формирование конечного массива specialCodes для вывода в рендер
                    $this->data['specialCodes'][$label][$code] = array_filter(
                      $this->value('specialPassengers'),
                      function ($model) use ($code) {
                        return isset($model['codes']) && in_array($code, $model['codes']);
                      });
                }
            }
        }
  
        // CCP-2083 Статусные пассажиры filter duplicates   ПЕРЕОПРЕДЕЛЕНГИЕ массива specialCodes для статусных пассажиров
        if ($groupedStatusPassengers) {
          foreach (self::$premiumPassengersCodesGroups as $code => $premiumPassengersCodesGroupsLabel) {
            if ($passengersGroups = ArrayHelper::getValue($groupedStatusPassengers, $premiumPassengersCodesGroupsLabel)) {
              $uniqueFilteredPassengers = [];
              foreach ($passengersGroups as $passengers) {
                if ($passengers) {
                  foreach ($passengers as $passenger) {
                    if (isset($passenger['seat']) && isset($passenger['fullName'])) {
                      $uniqueFilteredPassengers[$passenger['seat'] . $passenger['fullName']] = $passenger;
                    }
                  }
                }
              }
              // redeclare $this->data['specialCodes']['СТАТУСНЫЕ ПАССАЖИРЫ']
              $this->data['specialCodes']['СТАТУСНЫЕ ПАССАЖИРЫ'][$premiumPassengersCodesGroupsLabel] = $uniqueFilteredPassengers;
            }
          }
        }
        
        //getting free congratulations   ПОЗДРАВЛЕНИЯ НА БОРТУ С ДР!!!
        //$day = gmdate("d", strtotime($flight->std) + $flight->depAirport->utc_aims);
        //$month = gmdate("m", strtotime($flight->std) + $flight->depAirport->utc_aims);
        if ($this->passengers) {
            /** @var EdbPassenger $passenger */

            foreach ($this->passengers as $key => $passenger) {
              
              $gift_local_airport = date('m-d', strtotime($passenger->STD_LOCAL_DATE));
              $gift_birthday = date('m-d', strtotime($passenger->BIRTHDATE));
              
              //if (preg_match('/^\d\d\d\d-' . $month . '-' . $day . '/', $passenger->BIRTHDATE) &&
              if (
                $gift_local_airport == $gift_birthday //get BIRTHDAYS
                &&
                array_intersect(self::$premiumPassengersCodes, $this->value('specialPassengers.' . $key . '.codes') ?: []) //check for premium
                ) {
                  $this->data['gifts']['free'][$key] = [
                      'fio' => $passenger->getFullNameAmadeus(),
                      'seat' => ltrim($passenger->SEAT, '0'),
                      'type' => ''
                  ];
              }
            }
        }
        
        // group FOOD one by one
        // TODO временное решение потом надо сделать по УМУ! сортировка по поряку группы
        if (isset($this->data['specialCodes']) && ($this->data['specialCodes'])) {
            uksort($this->data['specialCodes'], function ($key1,$key2){
                return preg_match("/питани/iu", $key1)?-1:1;
            });
        }
    }

    public function composeData()
    {
        /** @var Flight $flight */
        $flight = $this->flight;

        $this->data = [];
        
        $this->composeAirports();
        $this->composeAircraft();
        $this->composeFlight();
        $this->composeCrew();
        
        if ($flight->menu && $flight->menu->rations) {
            $this->data['menu']['rations'] =
                implode(",", ArrayHelper::getColumn($flight->menu->rations, 'title'));
        }

        if ($ffms = $flight->getFfm()->all()) {
            foreach ($ffms as $key => $ffm) {
                $this->data['cargo']['ffm'][$key] = $ffm;
            }
        }

        if ($flightMails = $flight->getMails()->all()) {
            foreach ($flightMails as $key => $mail) {
                $this->data['cargo']['mail'][$key] = $mail;
            }
        }

        if ($loadDetails = $flight->getLoadDetails()->all()) {
            foreach ($loadDetails as $key => $detail) {
                $this->data['cargo']['details'][$key] = $detail;
            }
        }

        // additionalInfo
        $this->additionalInfo();

        // featuresFlight
        if ($flight->featuresFlight) {
            $this->data['features'] = $flight->featuresFlight;
        }

        $this->composeCodes();
    }

    public function value($key)
    {
        return ArrayHelper::getValue($this->data, $key);
    }

    public function composeHtml()
    {
        $widget = new BriefingWidget(['renderer' => $this]);
        return $widget->run();
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $this->composeData();
        return $this->composeHtml();
    }
    
    /**
     * метод фильтрует коды  FQTU FQTR FQTS FQTV при условии, что также есть коды 'SILVER','GOLD','PLATINUM','RUBY','EMERALD','SAPPHIRE'
     * @param array $codes
     * @deprecated
     */
    public function filterCodesOnDependency(array $codes)
    {
        $newCodes = [];
        $codesHaveDependencies = array_keys(self::$skipIfHasNoOnDependencyCodes);
        foreach ($codes ?: [] as $code) {
            if (in_array($code, $codesHaveDependencies)) {
                if (array_intersect(self::$skipIfHasNoOnDependencyCodes[$code], $codes)) {
                    $newCodes[] = $code;
                }
            } else {
                $newCodes[] = $code;
            }
        }
        
        return $newCodes;
    }
    
    public function composeCrewAsPassenger(EdbPassenger $passenger)
    {
        // если взрослый и служебный
        if ($passenger->isAdult() && $passenger->isOfficial()) {
                
            if ($employee = Employee::find()
                ->where(['ilike', 'fio_eng', $passenger->getLowerCaseFullName()])
                ->andWhere(['IS NOT', 'quals_list', NULL])
                ->andWhere(['<>', 'quals_list', ''])
                ->one())
            {
                // store 'БОРТПРОВОДНИКИ/ПИЛОТЫ'
                $this->data['specialCodes']['БОРТПРОВОДНИКИ/ПИЛОТЫ']['EMPL_'][] = [
                    'fullName' => $passenger->getFullNameAmadeus(),
                    'seat' => ltrim($passenger->SEAT, '0'),
                    'uniquePassenger' => $passenger->ID_ . $passenger->NAME . $passenger->SURNAME,
                ];
            }
        }
    }
}
