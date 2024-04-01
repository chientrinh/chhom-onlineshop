<?php
namespace frontend\widgets;

use Yii;
use \common\models\Product;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/widgets/HotProductList.php $
 * $Id: HotProductList.php 4248 2020-04-24 16:29:45Z mori $
 */

class HotProductList extends \yii\widgets\ListView
{
    public $tag      = 'hot';
    public $limit    = 24;
    public $pageSize = 4;

    public function __construct($config=[])
    {
        $this->dataProvider = new \yii\data\ActiveDataProvider([
            'query' => Product::find()->where(['restrict_id' => 0]),
        ]);

        parent::__construct($config);
    }

    public function init()
    {
        parent::init();

        $query = Product::find()->where(['restrict_id' => 0])->limit($this->limit);

        if('new' == $this->tag)
            $query->orderBy(['start_date' => SORT_DESC]);

        if ('recommend' == $this->tag) {
            $query->andWhere(['recommend_flg' => 1])->orderBy(['recommend_seq' => SORT_ASC]);
            $this->pageSize = 8;
        }

        if('hot' == $this->tag)
            $query->select(['dtb_product.*', 'COUNT(i.product_id) AS sold'])
                  ->join('JOIN', 'dtb_purchase_item i', 'i.product_id=dtb_product.product_id')
                  ->groupBy('i.product_id')
                  ->orderBy(['sold' => SORT_DESC])
                  ->join('JOIN', 'dtb_purchase p', 'p.purchase_id=i.purchase_id')
                  ->andWhere('p.create_date >= DATE_SUB(NOW(), INTERVAL 3 day)') // recent 3 days
                  ->andWhere('dtb_product.restrict_id < 99')
                  ->andWhere('dtb_product.category_id != 24'); // ライブ配信チケットを除外する 2020/04/24 kawai

        if('rand' == $this->tag)
            $query->orderBy(new \yii\db\Expression('RAND()'));

        $this->dataProvider = new \yii\data\ArrayDataProvider([
            'allModels'  => $query->all(),
            'pagination' => new \yii\data\Pagination(['totalCount' => $this->limit, 'defaultPageSize'=>$this->pageSize, 'pageParam' => $this->tag]),
        ]);

        if(! $this->itemView)
            $this->itemView = 'hot-product';
    }

    public function renderItem($model, $key, $index)
    {
        $viewFile = sprintf('%s/%s.php', $this->viewPath, $this->itemView);

        return $this->renderFile($viewFile, ['model'=>$model]);
    }

}
