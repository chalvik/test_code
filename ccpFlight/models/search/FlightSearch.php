<?php

namespace common\modules\ccpFlight\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\ccpFlight\models\Flight;
use yii\helpers\ArrayHelper;
use common\modules\ccpAirport\models\Airport;
use common\modules\ccpFlight\models\Carrier;
use common\modules\ccpAircraft\models\Aircraft;

/**
 * FlightSearch represents the model behind the search form
 * about `common\modules\ccpFlight\models\Flight`.
 * Class FlightSearch
 * @package common\modules\ccpFlight\models\search
 */
class FlightSearch extends Flight
{
    const OR_STRATEGY = "&&";
    const AND_STRATEGY = "@>";
    public $crew_roster_id;
    public $codes = [];
    public $codes_existed = [];
    public $has_fail_transfer = false;
    public $std_from;
    public $std_to;
    public $crew_pos;
    public $show_flight_transfer;



    public $code_search_strategy = '&&';
    const PU = 'PU';
    const FI = 'FI';
    const FA = 'FA';

    public static function mapCrewPos()
    {
        return [
            self::PU => self::PU,
            self::FI => self::FI,
            self::FA => self::FA,

        ];
    }


    public function formName()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'day', 'flt', 'carrier', 'aircraft_id', 'dep_airport_id', 'arr_airport_id', 'deleted_user_id', 'canceled', 'updated_user_id', 'interval', 'crew_roster_id'], 'integer'],
            [['fltDes', 'os', 'std', 'sta', 'etd', 'eta', 'arr_gate', 'dep_gate', 'arr_stand', 'dep_stand', 'arr_weather', 'dep_weather', 'deleted_at', 'created_at', 'updated_at', 'last_updated_at', 'estimated'], 'safe'],
            [['origin_std_date'], 'safe'],
            [['deleted', 'show_flight_transfer'], 'boolean'],
            [['has_fail_transfer'], 'boolean'],
            [['codes', 'codes_existed', 'std_from', 'std_to','crew_pos'], 'safe'],
            [['code_search_strategy'], 'safe'],
        ];
    }

    /**
     * @param null $names
     * @param array $except
     * @return array
     */
    public function getAttributes($names = null, $except = [])
    {
        $attributes = parent::getAttributes($names, $except);
        $attributes['show_flight_transfer']  = $this->show_flight_transfer;
        return $attributes;
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'code_search_strategy' => 'Принцип фильтрации',
            'show_flight_transfer' => 'Перенесенные рейсы',
            ]);
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */

    public function search($params)
    {
        $query = Flight::find()->with(['depAirport', 'arrAirport', 'aircraft', 'legs', 'crew', 'crew.employee', 'passengers']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        if (!$this->validate() || !array_values($params)) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'day' => $this->day,
            'flt' => $this->flt,
            'carrier' => $this->carrier,
            'aircraft_id' => $this->aircraft_id,
            'dep_airport_id' => $this->dep_airport_id,
            'arr_airport_id' => $this->arr_airport_id,
//            'std' => $this->std,
//            'sta' => $this->sta,
//            'etd' => $this->etd,
//            'eta' => $this->eta,
            'deleted' => $this->deleted,
            'deleted_at' => $this->deleted_at,
            'deleted_user_id' => $this->deleted_user_id,
            'canceled' => $this->canceled,
            'updated_user_id' => $this->updated_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'estimated' => $this->estimated,
            'interval' => $this->interval,
//            'last_updated_at' => $this->last_updated_at,

        ]);


        if ($this->codes_existed || $this->codes || $this->has_fail_transfer || $this->crew_pos) {
            if ($this->code_search_strategy == '@>') {

            } else {
                $this->code_search_strategy = "&&";
            }
            $query->joinWith('helper as helper');
            if ($this->codes) {
                // '{INF}'::text[]
                $query->andWhere("helper.codes {$this->code_search_strategy}  '{" . implode(",", $this->codes) . "}'::text[]");
            }
            if ($this->codes_existed) {
                $query->andWhere("helper.existed_codes {$this->code_search_strategy}  '{" . implode(",", $this->codes_existed) . "}'::text[]");
            }
            if ($this->has_fail_transfer) {
                $query->andWhere(['helper.has_fail_transfers' => true]);
            }

            if ($this->crew_pos) {
                $query->andWhere("helper.crew_pos && '{" . implode(",", $this->crew_pos) . "}'::text[]");
            }

        }


        if ($this->std) {
            $query->andFilterWhere([
                'between',
                'std',
                gmdate("Y-m-d 00:00", strtotime($this->std . " UTC")),
                gmdate("Y-m-d 23:59", strtotime($this->std . " UTC")),
            ]);
        }

        if ($this->std_from) {
            $query->andFilterWhere(['>=',
                'std',
                $this->std_from
            ]);
        }

        if ($this->std_to) {
            $query->andFilterWhere(['<=',
                'std',
                $this->std_to
            ]);
        }

        if ($this->origin_std_date) {
            $query->andFilterWhere(['=',
                'origin_std_date',
                $this->origin_std_date
            ]);
        }

        if ($this->show_flight_transfer) {
            $query->andFilterWhere(['>',
                'origin_std_date',
                '1980-01-01 00:00:00'
            ]);
        }





        if ($this->etd) {
            $query->andFilterWhere(['between',
                'etd',
                gmdate("Y-m-d 00:00", strtotime($this->etd . " UTC")),
                gmdate("Y-m-d 23:59", strtotime($this->etd . " UTC")),
            ]);
        }
        if ($this->sta) {
            $query->andFilterWhere([
                'between',
                'sta',
                gmdate("Y-m-d 00:00", strtotime($this->sta . " UTC")),
                gmdate("Y-m-d 23:59", strtotime($this->sta . " UTC")),
            ]);
        }
        if ($this->eta) {
            $query->andFilterWhere([
                'between',
                'eta',
                gmdate("Y-m-d 00:00", strtotime($this->eta . " UTC")),
                gmdate("Y-m-d 23:59", strtotime($this->eta . " UTC")),
            ]);
        }
        if ($this->last_updated_at) {
            $query->andFilterWhere([
                'between',
                'last_updated_at',
                gmdate("Y-m-d 00:00", strtotime($this->last_updated_at . " UTC")),
                gmdate("Y-m-d 23:59", strtotime($this->last_updated_at . " UTC")),
            ]);
        }




        $query->andFilterWhere(['like', 'fltDes', $this->fltDes])
            ->andFilterWhere(['like', 'os', $this->os])
            ->andFilterWhere(['like', 'arr_gate', $this->arr_gate])
            ->andFilterWhere(['like', 'dep_gate', $this->dep_gate])
            ->andFilterWhere(['like', 'arr_stand', $this->arr_stand])
            ->andFilterWhere(['like', 'dep_stand', $this->dep_stand])
            ->andFilterWhere(['like', 'arr_weather', $this->arr_weather])
            ->andFilterWhere(['like', 'dep_weather', $this->dep_weather]);

        if ($this->crew_roster_id) {
            $query->joinWith(['crew']);
            $query->andWhere(['ccp_flight_leg_crew.roster_id' => $this->crew_roster_id]);
        }

        return $dataProvider;
    }

    public static function listAirports()
    {
        return ArrayHelper::map(Airport::find()->all(), 'id', 'iata');
    }
    
    public static function listAircrafts()
    {
        return ArrayHelper::map(Aircraft::find()->all(), 'id', function ($data) {
            return $data->reg . " (". $data->id . ")";
        }, 'name');
    }

    public static function mapStrategy()
    {
        return [
            self::AND_STRATEGY => "AND",
            self::OR_STRATEGY => 'OR'
        ];
    }

    public static function listArrivalGates()
    {
        return ArrayHelper::map(Flight::find()->select(['arr_gate'])->distinct()->all(), 'arr_gate', 'arr_gate');
    }

    public static function listDepartureGates()
    {
        return ArrayHelper::map(Flight::find()->select(['dep_gate'])->distinct()->all(), 'dep_gate', 'dep_gate');
    }

    public static function listFlts()
    {
        return ArrayHelper::map(Flight::find()->select(['flt'])->distinct()->all(), 'flt', 'flt');
    }

    public static function listCarriers()
    {
        return ArrayHelper::map(
            Flight::find()->select(['carrier'])->distinct()->all(),
            'carrier', function ($data) {
            return Carrier::$carrier_list_aims[$data->carrier] ?? [];
        }
        );
    }
}
