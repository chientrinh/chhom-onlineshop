<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SearchCustomer represents the model behind the search form about `common\models\Customer`.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/models/SearchProduct.php $
 * $Id: SearchProduct.php 1687 2015-10-18 15:44:27Z mori $
 */
class SearchProduct extends \common\models\Product
{
    public $company;
    public $keywords;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'category_id', 'price'], 'integer'],
            [['name','code','kana','summary','description'], 'string'],
            [['start_date','expire_date'], 'date','format'=>'yyyy-MM-dd HH:mm:ii'],
            [['product_id','category_id','name','code','kana','price','summary','description'], 'safe'],
            ['company', 'exist', 'targetClass'=>\common\models\Company::className(), 'targetAttribute'=>'company_id', 'when'=> function($model){ return $model->company; }],
            ['company', 'safe', 'on'=>'search'],
            ['keywords','filter', 'filter'=> function($value) { return \common\components\Romaji2Kana::translate($value); }, 'skipOnEmpty'=>true ],
            ['kana', 'filter', 'filter'=> function($value) { return \common\components\Romaji2Kana::translate($value,'hiragana'); }, 'skipOnEmpty'=>true ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return parent::scenarios();
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
        $query = parent::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->sort = self::appendSortAttributes($dataProvider->sort);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            $this->tableName().'.product_id'  => $this->product_id,
            $this->tableName().'.category_id' => $this->category_id,
            $this->tableName().'.code'        => $this->code,
            $this->tableName().'.price'       => $this->price,
        ]);

        if($this->company)
            $query->innerJoinWith(['seller'])->andFilterWhere(['mtb_category.seller_id' => $this->company]);

        $query
            ->andFilterWhere(['like', 'name',        $this->name])
            ->andFilterWhere(['like', 'code',        $this->code])
            ->andFilterWhere(['like', 'kana',        $this->kana])
            ->andFilterWhere(['like', 'summary',     $this->summary])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['<=','start_date', new \yii\db\Expression('NOW()')])
            ->andWhere('expire_date > NOW() OR expire_date IS NULL');

        if($this->keywords)
            foreach(explode(' ', $this->keywords) as $item)
            {
                $query->andFilterWhere([
                    'or',
                    ['like','name',$item],
                    ['like','kana',$item],
                    ['like','code',$item],
                    ['like','price',$item],
                ]);
            }

        return $dataProvider;
    }

    private static function appendSortAttributes(\yii\data\Sort $sort)
    {
        $sort->attributes['name'] = [
            'asc'  => ['kana' => SORT_ASC ],
            'desc' => ['kana' => SORT_DESC],
        ];

        return $sort;
    }
}
