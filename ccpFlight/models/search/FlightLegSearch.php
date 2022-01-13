<?php
namespace common\modules\ccpFlight\models\search;

use common\modules\ccpFlight\models\FlightLeg;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * FlightLegSearch represents the model behind the search form
 * about `common\modules\ccpFlight\models\FlightLeg`.
 * Class FlightLegSearch
 * @package common\modules\ccpFlight\models\search
 */
class FlightLegSearch extends FlightLeg
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'flight_id', 'day', 'flt', 'dep_airport_id', 'carrier', 'arr_airport_id', 'aircraft_id'], 'integer'],
            [['dep', 'legcd', 'arr', 'ac', 'reg', 'adate', 'aroute', 'std', 'sta', 'etd', 'eta', 'blof', 'tkof', 'tdown',
                'blon', 'dep_gate', 'arr_gate', 'dep_gate', 'arr_stand', 'created_at', 'updated_at', 'last_updated_at',
                'last_load_updated_at', 'last_restriction_updated_at'], 'safe'],
            [['canceled'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
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
        $query = FlightLeg::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'flight_id' => $this->flight_id,
            'day' => $this->day,
            'flt' => $this->flt,
            'dep_airport_id' => $this->dep_airport_id,
            'carrier' => $this->carrier,
            'arr_airport_id' => $this->arr_airport_id,
            'aircraft_id' => $this->aircraft_id,
            'canceled' => $this->canceled,
            'std' => $this->std,
            'sta' => $this->sta,
            'etd' => $this->etd,
            'eta' => $this->eta,
            'blof' => $this->blof,
            'tkof' => $this->tkof,
            'tdown' => $this->tdown,
            'blon' => $this->blon,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'last_updated_at' => $this->last_updated_at,
            'last_load_updated_at' => $this->last_load_updated_at,
            'last_restriction_updated_at' => $this->last_restriction_updated_at,
        ]);

        $query->andFilterWhere(['like', 'dep', $this->dep])
            ->andFilterWhere(['like', 'legcd', $this->legcd])
            ->andFilterWhere(['like', 'arr', $this->arr])
            ->andFilterWhere(['like', 'ac', $this->ac])
            ->andFilterWhere(['like', 'reg', $this->reg])
            ->andFilterWhere(['like', 'adate', $this->adate])
            ->andFilterWhere(['like', 'aroute', $this->aroute])
            ->andFilterWhere(['like', 'dep_gate', $this->dep_gate])
            ->andFilterWhere(['like', 'arr_gate', $this->arr_gate])
            ->andFilterWhere(['like', 'arr_stand', $this->arr_stand])
            ->andFilterWhere(['like', 'dep_stand', $this->dep_stand]);

        return $dataProvider;
    }
}
