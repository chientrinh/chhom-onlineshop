<?php
namespace frontend\widgets;

use Yii;
use \yii\helpers\Html;
use \yii\helpers\Url;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/widgets/CategoryNav.php $
 * $Id: CategoryNav.php 4067 2018-11-28 08:10:14Z kawai $
 */

class CategoryNav extends \yii\base\Widget
{
    public $company;
    public $category;

    public function __construct($config=[])
    {
        parent::__construct($config);
    }

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        return \yii\widgets\ListView::widget([
            'dataProvider' => $this->getDataProvider(),
            'itemView'     => function ($model, $key, $index, $widget)
            {
                $category = \common\models\Category::findOne($model->category_id);
                $text  = $model->html_ja . Html::tag('em', $model->en, ['class'=>'Eng']);
                $route = sprintf('/category/%s', Html::encode($model->ja));

                return sprintf('<div class="col-md-3 cat0%d"><p>', $index + 1)
                     . Html::a($text, [ $route ], [
                         'style' => $model->active ? '' : 'opacity:0.4',
                     ])
                     . '</p></div>';
            },
            'id'           => 'w4',
            'options'      => ['class' => 'list-view Cat-Area for-smart-768'],
            'layout'       => '{items}',
        ]);
    }

    private function getDataProvider()
    {
        $allModels = [
            [
                'category_id' => 1, // and others, represent it by category_id=1
                'ja'          => '自然食品',
                'en'          => 'Foods',
                'active'      => true,
            ],
//            [
//                'category_id' => 3, // and others
//                'ja'          => '書籍',
//                'en'          => 'Books',
//                'active'      => true,
//            ],
            [
                'category_id' => 4,
                'ja'          => '自然化粧品',
                'en'          => 'Cosmetics',
                'active'      => true,
            ],
            [
                'category_id' => 6,
                'ja'          => 'レメディー・ハーブ酒',
                'en'          => 'Remedy / Tincture',
                'active'      => true,
            ],
            [
                'category_id' => '10',
                'ja'          => '雑貨・衣類',
                'en'          => 'Goods / Clothes',
                'active'      => true,
            ],
            [
                'category_id' => 13, // and others
                'ja'          => 'イベント・各種サービス',
                'en'          => 'Event / Service',
                'active'      => true,
            ],
        ];
        foreach($allModels as $k => $model)
        {
            $model         = (object) $model;
            $model->active = $this->isActiveItem($model);
            $model->html_ja = $model->ja;

            if(mb_strlen($model->ja) >= 10) {
                $model->html_ja     = preg_replace('/(・.+)$/u', '<span>$1</span>', $model->ja);
            }

            $allModels[$k] = $model;
        }

        return new \yii\data\ArrayDataProvider([
            'allModels' => $allModels
        ]);
    }

    private function isActiveItem($model)
    {

        if($this->category && ! preg_match(sprintf('/%s/u', $this->category->name), $model->ja))
            return false;

        return true;
    }

}
