<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use \common\components\ean13\CheckDigit;
use \common\models\Vegetable;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/models/Vegetable.php $
 * $Id: Vegetable.php 2933 2016-10-08 02:47:03Z mori $
 *
 * This is the model class for table "mtb_vegetable".
 *
 * @property integer $veg_id
 * @property string $name
 * @property string $kana
 * @property string $create_date
 * @property string $update_date
 */
class SearchVegetable extends Vegetable
{
    public $keywords;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['veg_id', 'division', 'origin_area', 'name', 'kana', 'print_name', 'keywords'], 'safe']
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
        $query = Vegetable::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['dsp_priority' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'veg_id' => $this->veg_id,
            'division' => $this->division,
        ]);

        $query->andFilterWhere(['like', 'origin_area', $this->origin_area])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'kana', $this->kana]);
            // ->andFilterWhere(['like', 'print_name', $this->print_name]);

        if($this->keywords)
            foreach(explode(' ', $this->keywords) as $item)
            {
                $query->andFilterWhere([
                    'or',
                    ['like', 'kana', $item],
                    ['like', 'name', $item],
                ]);
            }

        if($this->print_name)
            $this->seachPrintName($query);

        return $dataProvider;
    }

    public function seachPrintName($query)
    {
        $words = explode('　', $this->print_name); // 分割

        foreach($words as $word)
            $query->andFilterWhere(['OR',
                ['like', 'print_name',  $word],
                ['like', 'kana',        $word],
                ['like', 'name',        $word],
            ]);
        
        return $query;
    }
}