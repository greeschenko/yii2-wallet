<?php

namespace greeschenko\wallet\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * WalletSearch represents the model behind the search form of `greeschenko\wallet\models\Wallet`.
 */
class WalletSearch extends Wallet
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at', 'to_user', 'from_user', 'deposit', 'type', 'status'], 'integer'],
            [['sum'], 'number'],
            [['msg'], 'safe'],
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
     * Creates data provider instance with search query applied.
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Wallet::find();

        $query->orderBy('created_at DESC');

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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'to_user' => $this->to_user,
            'from_user' => $this->from_user,
            'sum' => $this->sum,
            'deposit' => $this->deposit,
            'type' => $this->type,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'msg', $this->msg]);

        return $dataProvider;
    }
}
