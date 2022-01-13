<?php

namespace common\modules\scheduler\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\scheduler\models\SchedulerTaskLog;

/**
 * SchedulerTaskLogSearch represents the model behind the
 * search form about `common\modules\scheduler\models\SchedulerTaskLog`.
 * Class SchedulerTaskLogSearch
 * @property $task_id integer
 * @package common\modules\scheduler\models\search
 */
class SchedulerTaskLogSearch extends SchedulerTaskLog
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
            [['id', 'task_run_id', 'status', 'task_id'], 'integer'],
            [['message', 'created_at'], 'safe'],
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
        $query = SchedulerTaskLog::find()->joinWith('run');

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

        $query->andFilterWhere([
            'scheduler_task_run.task_id' => $this->task_id,
        ]);

        // grid filtering conditions
        $query->andFilterWhere([
            'scheduler_task_log.id' => $this->id,
            'scheduler_task_log.task_run_id' => $this->task_run_id,
            'scheduler_task_log.status' => $this->status,
            'scheduler_task_log.created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'message', $this->message]);

        return $dataProvider;
    }
}
