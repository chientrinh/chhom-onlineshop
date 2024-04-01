<?php

namespace frontend\widgets;

use Yii;
use \yii\helpers\Html;

/**
 * @link many thanks to https://github.com/RezaSR/yii2-ButtonDropdownSorter/blob/master/ButtonDropdownSorter.php
 * @link $URL: https://tarax.toyouke.com/svn/MALL/frontend/widgets/LinkSorter.php $
 * @version $Id: LinkSorter.php 1651 2015-10-12 16:03:16Z mori $
 */
class LinkSorter extends \yii\widgets\LinkSorter
{
    /**
     * @var string lablel of dropdown button
     */
    public $label;
    public $linkOptions = [];
    /**
     * @var Sort the sort definition
     */
    public $sort;

    /**
     * @var array list of the attributes that support sorting. If not set, it will be determined
     *      using [[Sort::attributes]].
     */
    public $attributes;

    public function run()
    {
        echo $this->renderSortLinks();
    }

    protected function renderSortLinks()
    {
        $attributes = empty($this->attributes) ? array_keys($this->sort->attributes) : $this->attributes;

        $links = [];
        foreach ($attributes as $name) {
            $links[$name] = $this->sort->link($name, $this->linkOptions);
        }

        $params = Yii::$app->request->queryParams;
        $isGrid = Yii::$app->request->getQueryParam('grid');

        $gridUrl = $listUrl = \yii\helpers\Url::current($params);
        if($isGrid)
        {
            $params['grid'] = 0;
            $listUrl = \yii\helpers\Url::current($params);
        }
        else
        {
            $params['grid'] = 1;
            $gridUrl = \yii\helpers\Url::current($params);
        }

        return sprintf('<div class="Sorter">
					<h4>並び替え：</h4>
					<ul title="検索結果を並べ替える">
						<li class="%s">%s</li>
						<li class="%s">%s</li>
					</ul>
					<h4>表示形式：</h4>
					<ul title="表示形式の切り替え">
						<li class="%s"><a href="%s" class="btn btn-default">画像あり</a></li>
						<li class="%s"><a href="%s" class="btn btn-default">テキストのみ</a></li>
					</ul>
				</div>',
                       (isset($params['sort']) && (in_array($params['sort'], ['name','-name']))) ? 'active' : '',
                       $links['name'],
                       (isset($params['sort']) && (in_array($params['sort'], ['price','-price']))) ? 'active' : '',
                       $links['price'],
                       $isGrid ? ''       : 'active', $listUrl,
                       $isGrid ? 'active' : '',       $gridUrl
        );
    }

    private function urlWithGrid()
    {
        $params = Yii::$app->request->queryParams;
        $params['grid'] = 'true';
        return \yii\helpers\Url::current($params);
    }
    private function urlWithoutGrid()
    {
        $params = Yii::$app->request->queryParams;
        unset($params['grid']);
        return \yii\helpers\Url::current($params);
    }
}
