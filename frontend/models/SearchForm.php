<?php
namespace frontend\models;

use common\models\Product;
use yii\base\Model;
use Yii;

/**
 * Signup form
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/models/SearchForm.php $
 * $Id: SearchForm.php 3198 2017-02-26 05:44:08Z naito $
 */

class SearchForm extends Model
{
    public $keyword;
    public $category;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['keyword', 'filter', 'filter' => 'trim'],
            ['keyword', 'required'],
            ['keyword', 'string', 'max' => 255],
            ['category', 'required'],
            ['category', 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'keyword'  => "キーワード",
            'category' => "カテゴリ",
        ];
    }

    /*
      @return array like this:
        [
            ["1,2"]=> "食品",
            [3]=>     "出版",
            [4]=>     "化粧品",
            [5]=>     "喫食",
            [6]=>     "レメディー・ハーブ酒",
            [7]=>     "講演",
            ["9,10"]=>"雑貨",
        ];
    */
    public function getCategories()
    {
        $map = \yii\helpers\ArrayHelper::map(\common\models\Category::find()->all(), 'category_id', 'name');
        $rows = [];
        foreach($map as $id => $label)
        {
            if(isset($rows[$label]))
                $rows[$label] .= ','.$id; 
            else
                $rows[$label] = $id;
        }
        $rows = array_flip($rows);
        
        return $rows;
    }

    /**
     * Search products
     *
     * @return Product[]|null the searched products or null
     */
    public function search()
    {
        return null;
    }

}
