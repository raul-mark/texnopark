<?php

namespace app\models\shipment;

use yii\base\Model;
use yii\data\ActiveDataProvider;

use app\models\shipment\Shipment;

/**
 * ShipmentSearch represents the model behind the search form of `app\models\Shipment`.
 */
class ShipmentSearch extends Shipment
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'agent_id', 'type_id', 'sort'], 'integer'],
            [['date_shipment', 'fio', 'comment', 'date'], 'safe'],
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
        $query = Shipment::find();

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
            'status' => $this->status,
            'sort' => $this->sort,
            'date' => $this->date,
        ]);

        $query->andFilterWhere(['like', 'date_shipment', $this->date_shipment])
            ->andFilterWhere(['like', 'fio', $this->fio]);

        return $dataProvider;
    }
}
