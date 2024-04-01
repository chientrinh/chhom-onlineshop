<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use \common\components\ean13\CheckDigit;
use \common\models\Campaign;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/SearchCampaign.php $
 * $Id: $
 */
class SearchCampaign extends Campaign
{
    public $status = 1; // ステータスの検索初期値は「1:有効」

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['campaign_id', 'campaign_code', 'campaign_name', 'start_date', 'end_date', 'status', 'branch_id','streaming_id','free_shipping1','free_shipping2','pre_order'], 'safe'],
            // ['status', 'default', 'value' => 1],
            ['campaign_code', 'default', 'value' => null],
        ];
    }

    public function init()
    {
        parent::init();
        $this->campaign_code = null;
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
        $query = Campaign::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['campaign_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) 
        {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'campaign_id' => $this->campaign_id,
            'status'      => $this->status,
            'branch_id'   => $this->branch_id,
        ]);

        $query
            ->andFilterWhere(['like', 'campaign_code', $this->campaign_code])
            ->andFilterWhere(['like', 'campaign_name', $this->campaign_name]);

        if ($this->streaming_id)
            $query->andWhere(['streaming_id' => $this->streaming_id]);

        if ($this->free_shipping1 == "0") {
            $query->andWhere('free_shipping1 IS NULL');
        } else if ($this->free_shipping1 === "1") {
            $query->andWhere(['free_shipping1' => $this->free_shipping1]);
        }

        if ($this->free_shipping2 == "0") {
            $query->andWhere('free_shipping2 IS NULL');
        } else if ($this->free_shipping2 === "1") {
            $query->andWhere(['free_shipping2' => $this->free_shipping2]);
        }

        if ($this->pre_order === "0") {
            $query->andWhere('pre_order IS NULL');
        } else if ($this->pre_order === "1") {
            $query->andWhere(['free_shipping2' => $this->free_shipping2]);
        }

        if ($this->start_date)
        {
            $start_date = date('Y-m-d 00:00:00', strtotime($this->start_date));
            $query->andWhere('start_date >= :start_date', [':start_date' => $start_date]);
        }

        if ($this->end_date) 
        {
            $end_date = date('Y-m-d 23:59:59', strtotime($this->end_date));
            $query->andWhere('end_date <= :end_date', [':end_date' => $end_date]);
        }

        return $dataProvider;
    }
}