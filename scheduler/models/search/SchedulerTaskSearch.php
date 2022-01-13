<?php

namespace common\modules\scheduler\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\scheduler\models\SchedulerTask;

/**
 * Class SchedulerTaskSearch
 * @package common\modules\scheduler\models\search
 */
class SchedulerTaskSearch extends SchedulerTask
{

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
            [['id', 'period', 'user_created_id', 'user_updated_id', 'status'], 'integer'],
            [['title', 'class', 'created_at', 'updated_at'], 'safe'],
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
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = SchedulerTask::find();

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
            'period' => $this->period,
            'status' => $this->status,
            'user_created_id' => $this->user_created_id,
            'user_updated_id' => $this->user_updated_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'class', $this->class]);

        return $dataProvider;
    }
}
