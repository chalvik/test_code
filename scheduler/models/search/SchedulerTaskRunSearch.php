<?php

namespace common\modules\scheduler\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\scheduler\models\SchedulerTaskRun;

/**
 * Class SchedulerTaskRunSearch
 * @package common\modules\scheduler\models\search
 */
class SchedulerTaskRunSearch extends SchedulerTaskRun
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
            [['id', 'task_id', 'status'], 'integer'],
            [['started_at', 'finished_at'], 'safe'],
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
        $query = SchedulerTaskRun::find();

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
            'task_id' => $this->task_id,
            'started_at' => $this->started_at,
            'finished_at' => $this->finished_at,
            'status' => $this->status,
        ]);

        return $dataProvider;
    }
}
