<?php
namespace common\modules\ccpFlight\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\ccpFlight\models\FlightLegCrew;
use yii\helpers\ArrayHelper;
use common\modules\ccpFlight\models\Flight;
use common\modules\ccpEmployee\models\Employee;

/**
 * FlightLegCrewSearch represents the model behind the search form
 * about `common\modules\ccpFlight\models\FlightLegCrew`.
 * Class FlightLegCrewSearch
 * @package common\modules\ccpFlight\models\search
 */
class FlightLegCrewSearch extends FlightLegCrew
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'employee_id', 'flight_id', 'roster_id'], 'integer'],
            [['pos_code', 'id_dhd', 'pos_leg1', 'pos_flight', 'pos_pu', 'created_at', 'updated_at','last_updated_at'], 'safe'],
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
        $query = FlightLegCrew::find();

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
            'employee_id' => $this->employee_id,
            'flight_id' => $this->flight_id,
            'roster_id' => $this->roster_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'last_updated_at' => $this->last_updated_at,
            
            
        ]);

        $query->andFilterWhere(['like', 'pos_code', $this->pos_code])
            ->andFilterWhere(['like', 'id_dhd', $this->id_dhd])
            ->andFilterWhere(['like', 'pos_leg1', $this->pos_leg1])
            ->andFilterWhere(['like', 'pos_flight', $this->pos_flight])
            ->andFilterWhere(['like', 'pos_pu', $this->pos_pu]);

        return $dataProvider;
    }
    
    public static function listPostCodes()
    {
        $posCodes = \Yii::$app->db->createCommand('SELECT distinct(pos_code) FROM ccp_flight_leg_crew;')->queryAll();
        
        return ArrayHelper::map($posCodes, 'pos_code', 'pos_code');
    }
    
    public static function listFlts()
    {
        return ArrayHelper::map(Flight::find()->select(['flt'])->distinct()->all(), 'flt', 'flt');
    }
    
    public static function listEmployees()
    {
        return ArrayHelper::map(FlightLegCrew::find()->select(['employee_id'])->distinct()->all(), 'employee_id', 'employee_id');
    }
    
    public static function listCrew()
    {
        return ArrayHelper::map(
            FlightLegCrew::find()->select(['roster_id'])->distinct()->all(),
            'roster_id',
            'roster_id'
        );
    }
}
