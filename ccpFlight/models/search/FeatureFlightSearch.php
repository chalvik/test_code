<?php

namespace common\modules\ccpFlight\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\ccpFlight\models\FeatureFlight;
use yii\helpers\ArrayHelper;
use common\modules\ccpFlight\models\Flight;

/**
 * FeatureFlightSearch represents the model behind the search form
 * about `common\modules\ccpFlight\models\FeatureFlight`.
 * Class FeatureFlightSearch
 * @package common\modules\ccpFlight\models\search
 */
class FeatureFlightSearch extends FeatureFlight
{

    public $query;
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
            [['id', 'flight_flt', 'type'], 'integer'],
            [['note', 'start_date', 'end_date','query'], 'safe'],
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
        $query = FeatureFlight::find();

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
            'flight_flt' => $this->flight_flt,
            'type' => $this->type,
        ]);

        if ($this->start_date) {
            $query->andWhere(['>=', 'start_date', $this->start_date]);
        }

        if ($this->end_date) {
            $query->andWhere(['<=', 'end_date', $this->end_date]);
        }

        $query->andFilterWhere(['ilike', 'note', $this->note]);

        return $dataProvider;
    }

    public static function listFightFlts()
    {
        return ArrayHelper::map(Flight::find()->select(['flt'])->distinct()->all(), 'flt', 'flt');
    }
}
