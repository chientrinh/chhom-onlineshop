<?php
namespace common\modules\sodan\widgets;

use Yii;
use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\helpers\ArrayHelper;
use common\models\sodan\WaitList;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/widgets/WaitListGrid.php $
 * $Id: WaitListGrid.php 3851 2018-04-24 09:07:27Z mori $
 */

class WaitListGrid extends \yii\base\Widget
{
    public $dataProvider;
    public $searchModel;
    public $columns = [];
    public $attributes = ['wait_id','branch_id','homoeopath_id','client_id','note','expire_date'];

    public function init()
    {
        parent::init();

        if(! $this->dataProvider)
             $this->dataProvider = new \yii\data\ActiveDataProvider(['query' => Interview::find()]);

        foreach($this->getAllAttributes() as $params)
        {
            $key = $params['attribute'];

            if(in_array($key, $this->attributes))
                $this->columns[] = $params;
        }
    }

    public function run()
    {
        echo \yii\grid\GridView::widget([
            'dataProvider' => $this->dataProvider,
            'filterModel'  => $this->searchModel,
            'columns'      => $this->columns,

            'tableOptions' => ['class'=>'table table-condensed table-striped'],
            'layout'       => '{pager}{items}{summary}',
        ]);
    }

    private function getAllAttributes()
    {
        return [
            [
                'attribute' => 'wait_id',
                'format'    => 'html',
                'value'     => function($data){ return Html::a(sprintf('%06d',$data->wait_id),['wait-list/view','id'=>$data->wait_id]); },
            ],
            [
                'attribute' => 'branch_id',
                'format'    => 'html',
                'value'     => function($data){ if($data->branch) return $data->branch->name; },
                'filter'    => ArrayHelper::map(\common\models\Branch::find()->center()->asArray()->all(),'branch_id','name')
            ],
            [
                'attribute' => 'itv_date',
                'format'    => 'html',
                'value'     => function($data)
                {
                    if($data->itv_date)
                    {
                        $value = Yii::$app->formatter->asDate($data->itv_date,'php:Y-m-d');
                        return Html::a($value,['index','Interview[itv_date]'=>$value],['class'=>'btn-default'])
                                   . Yii::$app->formatter->asDate($data->itv_date,'php: D');;
                    }
                },
            ],
            [
                'attribute' => 'itv_time',
                'format'    => 'html',
                'value'     => function($data)
                {
                    if($data->itv_time)
                    {
                        $value = date('H:i', strtotime($data->itv_time));
                        return Html::a($value,['index','Interview[itv_time]'=>$value],['class'=>'btn-default']);
                    }
                },
            ],
            [
                'attribute' => 'homoeopath_id',
                'format'    => 'raw',
                'value'     => function($data)
                {
                    if($data->homoeopath)
                        return Html::a($data->homoeopath->homoeopathname,Url::current(['Interview[homoeopath_id]'=>$data->homoeopath_id]),['class'=>'btn-default']);
                },
            ],
            [
                'attribute' => 'client_id',
                'format'    => 'html',
                'value'     => function($data)
                {
                    if(! $data->client_id)
                        return;

                    $client = new \common\models\sodan\Client(['client_id' => $data->client_id]);

                    $html = '';

                    if($client->isAnimal())
                        $html .= Html::img(Url::base().'/img/paw.png',['class'=>'icon','title'=>'動物相談です']);

                    return $html;
                }
            ],
            [
                'attribute' => 'note',
                'format'    => 'text',
                'value'     => function($data){ return \yii\helpers\StringHelper::truncate($data->note,12); },
            ],
            [
                'attribute' => 'expire_date',
                'format'    => 'date',
            ],
        ];
    }

    public function getProducts()
    {
        return \yii\helpers\ArrayHelper::map(
            \common\models\Product::find()
                                  ->select(['product_id','name'])
                                  ->andWhere(['product_id' => \common\models\sodan\Interview::find()->select('product_id')->distinct()->column()])
                                  ->asArray()
                                  ->all(),
            'product_id', 'name');

    }
}
