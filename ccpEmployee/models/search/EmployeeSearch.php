<?php
namespace common\modules\ccpEmployee\models\search;

use common\components\PSqlDecoder;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\ccpEmployee\models\Employee;

/**
 * EmployeeSearch represents the model behind the search form about `common\modules\ccpEmployee\models\Employee`.
 * Class EmployeeSearch
 * @package common\modules\ccpEmployee\models\search
 * @param string $crewcatidx
 */
class EmployeeSearch extends Employee
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'roster_id', 'port_base_id', 'type'], 'integer'],
            [['name', 'fio_eng', 'fio_rus', 'quals_list', 'langs_list', 'port_base', 'block_hours', 'created_at', 'updated_at','last_updated_at', 'crewcatidx'], 'safe'],
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
        $query = Employee::find();
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
            'id'            => $this->id,
            'roster_id'     => $this->roster_id,
            'created_at'    => $this->created_at,
            'type'          => $this->type,
            'updated_at'    => $this->updated_at,
            'last_updated_at' => $this->last_updated_at,
            
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'fio_eng', $this->fio_eng])
            ->andFilterWhere(['like', 'fio_rus', $this->fio_rus])
            ->andFilterWhere(['like', 'quals_list', $this->quals_list])
            ->andFilterWhere(['like', 'langs_list', $this->langs_list])
            ->andFilterWhere(['like', 'port_base', $this->port_base])
            ->andFilterWhere(['like', 'block_hours', $this->block_hours]);

        $crewcatidx = explode(',', $this->crewcatidx);
        $query->andFilterWhere(["@>", "crewcatidx", PSqlDecoder::encodeArray($crewcatidx)]);


        return $dataProvider;
    }
}
