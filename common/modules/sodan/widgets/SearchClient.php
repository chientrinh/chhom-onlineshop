<?php
namespace common\modules\sodan\widgets;
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/widgets/SearchClient.php $
 * $Id: SearchClient.php 3847 2018-04-04 07:18:36Z naito $
 */

use \yii\helpers\ArrayHelper;
use \yii\helpers\Html;
use \yii\helpers\Url;
use \common\models\Customer;
use \common\models\sodan\WaitList;

class SearchClient extends \yii\base\Widget
{
    /*
     * @var string 検索する文字
     */
    public $keyword = null;

    /*
     * @var string Urlに渡す時のパラメタ名
     */
    public $param = 'client_id';

    public $mode = null;

    public function init()
    {
        parent::init();
    }

    public function getQuery()
    {
        if(! $this->keyword)
            return Customer::find()->andWhere('1 = 0');

        $keyword = \common\components\Romaji2Kana::translate($this->keyword, 'hiragana');
        $keyword = mb_convert_kana($keyword, 's');
        $keyword = trim($keyword);
        $keyword = explode(' ', $keyword);

        $query   = Customer::find()->active();
        $query->andWhere(['or',
                          ['like','CONCAT(kana01,kana02)',    $keyword],
                          ['like','CONCAT(name01,name02)',    $keyword],
                          ['like','CONCAT(tel01,tel02,tel03)',$keyword],
                          ['like', 'birth', str_replace('/', '-', $keyword)]
        ]);

        return $query;
    }

    public function run()
    {
        echo \yii\grid\GridView::widget([
            'dataProvider' => new \yii\data\ActiveDataProvider([
                'query'  => $this->query,
                'pagination' => [
                    'pageSize' => 20,
                    'pageParam' => 'scp', // stands for Search Client Page
                ],
            ]),
            'tableOptions' => ['class'=>'table-condensed'],
            'showHeader'   => false,
            'showOnEmpty'  => false,
            'emptyText'    => '',
            'layout'       => '{items}<span class="hint-block">{summary}</span>',
            'columns'      => [
                [
                    'attribute' => 'kana',
                    'format'    => 'text',
                ],
                [
                    'attribute' => 'name',
                    'format'    => 'text',
                ],
                [
                    'attribute' => 'tel',
                    'format'    => 'text',
                ],
                [
                    'attribute' => 'birth',
                    'format'    => 'text',
                    'value'     => function($data) {
                        return ($data->birth) ? date('Y/m/d', strtotime($data->birth)) : null;
                    }
                ],
                [
                    'attribute' => 'pref_id',
                    'format'    => 'text',
                    'value'     => function ($data) {
                        return "{$data->addr}";
                    }
                ],
                [
                    'label' => '',
                    'format' => 'html',
                    'value'  => function($data)
                    {
                        $link = (!$data->parent) ? Html::a('家族会員を作成する', ['/customer/create-child', 'parent_id' => $data->customer_id], ['class' => 'btn btn-default']) : '';

                        return $link;
                    },
                    'visible' => ($this->mode === 'client')
                ],
                [
                    'label' => '',
                    'format' => 'html',
                    'value'  => function($data)
                    {
                        $url = Url::current([$this->param => $data->customer_id]);

                        return Html::a('', $url, [
                            'class' => 'glyphicon glyphicon-plus btn btn-success',
                            'title' => 'クライアントを指定する',
                        ]);
                    }
                ],
            ]
        ]);
    }
}
