<?php

namespace common\modules\ccpFlight\models\helpers;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\ccpFlight\models\helpers\HelperFlightFeatures;

/**
 * HelperFlightFeaturesSearch represents the model behind the search form of `\common\modules\ccpFlight\models\helpers\HelperFlightFeatures`.
 */
class HelperFlightFeaturesSearch extends HelperFlightFeatures
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['flight_id'], 'integer'],
            [['codes', 'existed_codes', 'created_at'], 'safe'],
            [['has_fail_transfers'], 'boolean'],
        ];
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
        $query = HelperFlightFeatures::find();

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
            'has_fail_transfers' => $this->has_fail_transfers,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['ilike', 'codes', $this->codes])
            ->andFilterWhere(['ilike', 'existed_codes', $this->existed_codes]);

        return $dataProvider;
    }
}
