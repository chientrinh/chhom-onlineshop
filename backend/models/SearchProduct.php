<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use \common\models\Subcategory;
use \common\models\Product;

/**
 * SearchCustomer represents the model behind the search form about `common\models\Customer`.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/SearchProduct.php $
 * $Id: SearchProduct.php 3841 2018-03-03 04:03:35Z kawai $
 */
class SearchProduct extends Product
{
    public $company;
    public $keywords;
    public $subcategory;
    public $subcategory_id;
    public $categogies;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'category_id', 'price', 'restrict_id',], 'integer'],
            [['name','code','kana','summary','description'], 'string'],
            ['kana','filter', 'filter'=> function($value) { return mb_convert_kana($value,'c'); }],
            ['kana','filter', 'filter'=> function($value) { return \common\components\Romaji2Kana::translate($value,'hiragana'); }],
            [['category_id'],    'exist', 'targetClass' => \common\models\Category::className() ],
            [['subcategory_id'], 'exist', 'targetClass' => \common\models\Subcategory::className() ],
            [['start_date','expire_date'], 'date','format'=>'yyyy-MM-dd HH:mm:ii'],
            [['product_id','category_id','name','code','kana','price','summary','description'], 'safe'],
            ['company', 'exist', 'targetClass'=>\common\models\Company::className(), 'targetAttribute'=>'company_id', 'when'=> function($model){ return $model->company; }],
            // ['subcategory', 'exist', 'targetClass'=>\common\models\Subcategory::className(), 'targetAttribute'=>'subcategory_id','allowArray'=>true],
            [['company', 'keywords'], 'safe', 'on'=>'search'],
            // ['keywords','filter', 'filter'=> function($value) { return \common\components\Romaji2Kana::translate($value); }, 'skipOnEmpty'=>true ],
            ['in_stock','boolean']
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
    public function search($params, $recommend_flg = false)
    {
        $query = Product::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => [ 'defaultOrder' => ['product_id' => SORT_DESC] ],
        ]);
        $dataProvider->sort = self::appendSortAttributes($dataProvider->sort);

        $query->leftJoin(['m' => \common\models\ProductMaster::tableName()], Product::tableName().'.product_id=m.product_id')
                        ->leftJoin(['ps' => \common\models\ProductSubcategory::tableName()], 'm.ean13=ps.ean13')
                        ->leftJoin(['s' => \common\models\Subcategory::tableName()], 's.subcategory_id=ps.subcategory_id');

        // 表示名のない（空白）の商品は検索対象から除外する
        $query->andWhere(['not', ['m.name' => '']]);


        if($this->subcategory)
            $query->andWhere(['s.subcategory_id'=>$this->subcategory]);

        if($this->categogies)
            $query->andWhere([$this->tableName().'.category_id' => $this->categogies]);

        if($this->company)
            $query->innerJoinWith(['seller'])
                  ->andWhere(['mtb_category.seller_id' => $this->company]);

        // おすすめ商品検索時
        if ($recommend_flg) {
            $query->andWhere([$this->tableName().'.recommend_flg' => 1]);
        }

        $this->load($params);
        // if (!$this->validate()) {
        //     // uncomment the following line if you do not want to any records when validation fails
        //     // $query->where('0=1');
        //     return $dataProvider;
        // }

        $query->andFilterWhere([
            $this->tableName().'.product_id'  => $this->product_id,
            $this->tableName().'.category_id' => $this->category_id,
            $this->tableName().'.code'        => $this->code,
            $this->tableName().'.price'       => $this->price,
            $this->tableName().'.restrict_id' => $this->restrict_id,
            $this->tableName().'.in_stock'    => $this->in_stock,
            's.subcategory_id'    => $this->subcategory_id,
        ]);

        $query
            ->andFilterWhere(['like', $this->tableName().'.name',        $this->name])
            ->andFilterWhere(['like', $this->tableName().'.code',        $this->code])
            ->andFilterWhere(['like', $this->tableName().'.kana',        $this->kana])
            ->andFilterWhere(['like', $this->tableName().'.summary',     $this->summary])
            ->andFilterWhere(['like', $this->tableName().'.description', $this->description])
            ;

        if($this->keywords){
            foreach(explode(' ', $this->keywords) as $item)
            {
                $query->andFilterWhere([
                    'or',
                    ['like', $this->tableName().'.name',$item],
                    ['like', $this->tableName().'.kana',$item],
                    ['like', $this->tableName().'.code',$item],
                    ['like', $this->tableName().'.price',$item],
                ]);
            }
        }
        $query->distinct();

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
