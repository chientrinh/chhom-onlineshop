<?php

namespace backend\models;

use Yii;
use \common\models\ProductMaster;
use \common\models\RemedyVial;

/**
 * ProductSearch represents the model behind the search form about `common\models\Product`.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/models/SearchProductMaster.php $
 * $Id: SearchProductMaster.php 2730 2016-07-17 01:25:20Z mori $
 */
class SearchProductMaster extends ProductMaster
{
    public $category_id;
    public $subcategory_id; /* フィルター用 */
    public $subcategories;  /* 初期条件用（対象サブカテゴリー全てを配列で指定する想定） */
    public $parent_id;

    public $company;
    public $company_id;
    public $customer;
    public $branch_id;

    public $keywords;
    public $ean13;
    public $name;
    public $kana;
    public $price;

    public $in_stock;
    // public $restrict_id; // mtb_remedy.restrict_id用

    public $product_id;
    public $remedy_id;
    public $potency_id; /* フィルター用 */
    public $potencies;  /* 初期条件用（デフォルト表示対象ポーテンシー） */
    public $vial_id;    /* フィルター用 */
    public $vials;      /* 初期条件用（デフォルト表示対象容器） */

    /* @inheritdoc */
    public function rules()
    {
        return [
            [['category_id','subcategory_id', 'parent_id'], 'integer'],
            [['category_id'],    'exist', 'targetClass' => \common\models\Category::className() ],
            [['subcategory_id'], 'exist', 'targetClass' => \common\models\Subcategory::className() ],
            [['vial_id'],        'exist', 'targetClass' => \common\models\RemedyVial::className() ],
            [['potency_id'],     'exist', 'targetClass' => \common\models\RemedyPotency::className() ],
            [['keywords'], 'filter', 'filter' => 'trim', 'skipOnArray' => true],
            [['keywords'], 'string', 'max' => 255],
            [['kana'], 'string'],
             [['name', 'price'], 'safe']
            ];
    }

    /* @inheritdoc */
    public function attributeLabels()
    {
        return [
            'category' => "カテゴリー",
            'category_id' => "カテゴリー",
            'keywords' => "キーワード",
        ];
    }

    /* @inheritdoc */
    public function attributes()
    {
        return ['category','keywords'];
    }

    /* @inheritdoc */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return parent::scenarios();
    }


    public function getSubCategory()
    {
        return $this->hasOne(Subcategory::className(), ['subcategory_id' => 'subcategory_id'])
                    ->viaTable(ProductSubcategory::tableName(), ['ean13' => 'ean13']);
    }

    public function init()
    {
        parent::init();

        if(null === $this->customer)
            $this->customer = new \common\models\NullCustomer();
    }

    public function search($params = [], $remedyStock=false)
    {
        $query = ProductMaster::find()->where(['or',
                                               ['vial_id' => null ],
                                               ['not', ['vial_id' => RemedyVial::DROP]]]);

        if($this->customer->customer_id)
            $query->andWhere(['<=','restrict_id',$this->customer->grade_id]);
        else // isGuest
            $query->andWhere(['restrict_id' => 0]);

        $this->load($params);

        $query->andFilterWhere([
            'remedy_id'   => $this->remedy_id,
            'potency_id'  => $this->potency_id,
            'vial_id'     => $this->vial_id,
            'category_id' => $this->category_id,
        ]);

        if($this->subcategory_id)
            $query->subcategory($this->subcategory_id);

        if($this->company)
            $query->company($this->company);
        
        if(strlen($this->kana))
            $query->andWhere(['like', 'name', $this->kana]);

        if($this->keywords &&
          ($keywords = \common\components\KanaHelper::split($this->keywords))
        ){
            $query->andFilterWhere(['OR',
                                   ['like', 'kana',        $keywords],
                                   ['like', 'name',        $keywords],
            ]);
            $this->keywords = implode(' ', $keywords); // restore keywords
        }

        return new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['dsp_priority' => SORT_DESC]],
        ]);
    }
    
    /**
     * 化粧品・食品用
     * @param array $params 検索条件
     */
    public function searchCosmeAndFood($params = [])
    {
        $query = ProductMaster::find()
                    ->joinWith('subcategories', true, 'INNER JOIN')
                    ->andWhere(['or', 
                                    ['mtb_subcategory.subcategory_id' => $this->subcategories], 
                                    ['parent_id' => $this->subcategories]
                    ]);

        $this->load($params);

        if ($this->branch_id === \common\models\Branch::PKEY_ATAMI) {
            $query->andWhere('mvtb_product_master.company_id <> ' . \common\models\Company::PKEY_TY);
        }

        /* 以下は画面上の検索条件 */
        $query->andFilterWhere(['or', 
                                    ['mtb_subcategory.subcategory_id' => $this->subcategory_id], 
                                    ['parent_id' => $this->subcategory_id]
                    ]);
        $query->andFilterWhere(['like', 'mvtb_product_master.name',$this->name]);
        
        
        if(strlen($this->name))
            $query->andWhere(['like', 'mvtb_product_master.name', $this->name]);
        
        if(strlen($this->price))
            $query->andWhere(['mvtb_product_master.price' => $this->price]);
        
        if(! $this->keywords && $keywords = Yii::$app->request->get('keywords'))
            $this->keywords = $keywords;

        $this->keywords($query);

        return new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['dsp_priority' => SORT_DESC]],
        ]);
    }

    /**
     * レメディー全品用（remedy/MT/FE/FE2）
     * @param array $params 検索条件
     */
    public function searchAllRemedy($params = [])
    {

        $query = ProductMaster::find()
                    ->joinWith('remedy', true, 'LEFT JOIN');

        $condition = ['potency_id' => [\common\models\RemedyPotency::MT, \common\models\RemedyPotency::FE, \common\models\RemedyPotency::FE2]];
        $query->andWhere(['not', $condition]);
        $query->andWhere(['in', 'vial_id', \yii\helpers\ArrayHelper::getColumn(
            \common\models\RemedyVial::find()->remedy()->all(),'vial_id'
        )]);
        $query->groupBy('mvtb_product_master.ean13');
        $query->orderBy('abbr');


        if($this->vials)
            $query->andWhere(['mvtb_product_master.vial_id'=>$this->vials]);

        if($this->potencies)
            $query->andWhere(['mvtb_product_master.potency_id'=>$this->potencies]);

        $this->load($params);

        $query->andFilterWhere([
            'mvtb_product_master.remedy_id'  => $this->remedy_id,
            'mvtb_product_master.potency_id' => $this->potency_id,
            'mvtb_product_master.vial_id'    => $this->vial_id,
        ]);

        if(strlen($this->kana))
            $query->andWhere(['like', 'mvtb_product_master.name', $this->kana]);

        if(strlen($this->price))
            $query->andWhere(['like', 'mvtb_product_master.price', $this->price]);

        $this->keywords($query);
  
        return new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['dsp_priority' => SORT_DESC]],
        ]);
    }

    /**
     * レメディー用（remedy/MT/FE/FE2）
     * @param array $params 検索条件
     */
    public function searchRemedy($params = [])
    {
        $query = ProductMaster::find()
                    ->joinWith('subcategories', true, 'INNER JOIN')
                    ->joinWith('remedy', true, 'INNER JOIN');

        if($this->vials)
            $query->andWhere(['mvtb_product_master.vial_id'=>$this->vials]);

        if($this->potencies)
            $query->andWhere(['mvtb_product_master.potency_id'=>$this->potencies]);

        if($this->parent_id)
            $query->andWhere(['parent_id' => $this->parent_id]);

        if($this->subcategories)
            $query->andWhere(['mtb_subcategory.subcategory_id'=>$this->subcategories]);

        $this->load($params);

        $query->andFilterWhere([
            'mtb_subcategory.subcategory_id' => $this->subcategory_id,
            'mvtb_product_master.remedy_id'  => $this->remedy_id,
            'mvtb_product_master.potency_id' => $this->potency_id,
            'mvtb_product_master.vial_id'    => $this->vial_id,
        ]);

        if(strlen($this->kana))
            $query->andWhere(['like', 'mvtb_product_master.name', $this->kana]);

        if(strlen($this->price))
            $query->andWhere(['like', 'mvtb_product_master.price', $this->price]);

        $this->keywords($query);
  
        return new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['dsp_priority' => SORT_DESC]],
        ]);
    }

    /**
     * セット単品・キット単品用
     * @param array $params 検索条件
     */
    public function searchSeparately($params = [], $remdyOnly = true)
    {
        $query = ProductMaster::find()
                    ->joinWith('subcategories', true, 'INNER JOIN');
                    
        if ($remdyOnly)
            $query->andWhere('product_id is null');
        
        if ($this->parent_id && $this->subcategories) {
            $query->andWhere(['or', 
                                ['mtb_subcategory.subcategory_id' => $this->subcategories], 
                                ['parent_id' => $this->parent_id]
            ]);

        } elseif ($this->parent_id) {
            $query->andWhere(['parent_id' => $this->parent_id]);
        } elseif ($this->subcategories) {
            $query->andWhere(['mtb_subcategory.subcategory_id'=>$this->subcategories]);
        }

        $this->load($params);

        /* 以下は画面上の検索条件 */
        $query->andFilterWhere([
            'mtb_subcategory.subcategory_id' => $this->subcategory_id,
            'remedy_id'  => $this->remedy_id,
            'potency_id' => $this->potency_id,
            'vial_id'    => $this->vial_id,
        ]);
      
        if(strlen($this->kana))
            $query->andWhere(['like', 'mvtb_product_master.name', $this->kana]);

        if(strlen($this->price))
            $query->andWhere(['like', 'mvtb_product_master.price', $this->price]);

        if(! $this->keywords && $keywords = Yii::$app->request->get('keywords'))
            $this->keywords = $keywords;

        $this->keywords($query);

        return new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['dsp_priority' => SORT_DESC]],
        ]);
    }

    public function keywords($query)
    {
        foreach(explode(' ', $this->keywords) as $item)
            {
                $query->andFilterWhere([
                    'or',
                    ['like', 'mvtb_product_master.kana', $item],
                    ['like', 'mvtb_product_master.name', $item],
                ]);
            }

        return $query;
    }
}
