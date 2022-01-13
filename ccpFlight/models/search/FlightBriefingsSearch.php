<?php

namespace common\modules\ccpFlight\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\ccpFlight\models\FlightBriefings;
use yii\helpers\ArrayHelper;
use common\modules\ccpFlight\models\Flight;

/**
 * FlightBriefingsSearch represents the model behind the search form of `common\modules\ccpFlight\models\FlightBriefings`.
 */
class FlightBriefingsSearch extends Model
{

    public $flight_id;
    public $title;
    public $std;
    public $flt;
    public $file_id;
    public $roster_id;
    public $user_id;
    public $status;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['flight_id', 'flt', 'user_id', 'roster_id', 'file_id', 'status'], 'integer'],
            [['title', 'std'], 'safe'],
        ];
    }


    public function formName()
    {
        return '';
    }

    /**
     * {@inheritdoc}
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
        $query = FlightBriefings::find();

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
            'flight_id' => $this->flight_id,
            'std' => $this->std,
            'flt' => $this->flt,
            'user_id' => $this->user_id,
            'roster_id' => $this->roster_id,
        ]);

        $query->andFilterWhere(['ilike', 'title', $this->title]);

        return $dataProvider;
    }
    
    public static function listFlts()
    {
        return ArrayHelper::map(Flight::find()->select(['flt'])->distinct()->all(), 'flt', 'flt');
    }
    
    public static function listFlights()
    {
        return ArrayHelper::map(FlightBriefings::find()->select(['flight_id'])->distinct()->all(), 'flight_id', 'flight_id');
    }
}
