<?php
namespace common\modules\sodan\widgets;

use Yii;
use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\helpers\ArrayHelper;
use common\models\sodan\Interview;
use \common\models\sodan\InterviewStatus;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/widgets/InterviewGrid.php $
 * $Id: InterviewGrid.php 4125 2019-03-20 10:00:56Z kawai $
 */

class InterviewGrid extends \yii\base\Widget
{
    public $dataProvider;
    public $searchModel;
    public $homoeopath;
    public $columns = [];
    public $attributes = ['itv_id','branch_id','itv_date','itv_time','homoeopath_id','client_id'];

    public function init()
    {
        parent::init();

        if(! $this->dataProvider)
             $this->dataProvider = new \yii\data\ActiveDataProvider(['query' => Interview::find()]);

        $allAttributes = $this->getAllAttributes();
        $column = ArrayHelper::getColumn($allAttributes,'attribute');

        foreach($this->attributes as $name)
        {
            if(! in_array($name, $column))
                $this->columns[] = $name;

            else
            foreach($column as $k => $content)
                if($name === $content)
                { $this->columns[] = $allAttributes[$k]; break; }
        }
    }

    public function run()
    {
        echo \yii\grid\GridView::widget([
            'dataProvider' => $this->dataProvider,
            'filterModel'  => $this->searchModel,
            'columns'      => $this->columns,
            'pager'        => ['maxButtonCount' => 20],

            'tableOptions' => ['class'=>'table table-condensed table-striped'],
            'layout'       => '{pager}{items}{summary}',
        ]);
    }

    private function getAllAttributes()
    {
        $query = \common\models\Branch::find()->center()->asArray();
        $branch = ArrayHelper::map($query->all(),'branch_id','name');

        foreach($branch as $k => $v)
            $branch[$k] = preg_replace('/日本ホメオパシーセンター|総?本部/u', '', $v);

        return [
            [
                'attribute' => 'itv_id',
                'format'    => 'html',
                'value'     => function($data)
                {
                    $text  = Html::a(sprintf('%06d',$data->itv_id),['interview/view','id'=>$data->itv_id,'target'=>'interview']);
                    $text .= ($data->open_flg) ? ' ' . Html::tag('i', '', ['title' => '公開枠', 'style' => 'color:#337ab7;font-size:1.2em;', 'class' => 'glyphicon glyphicon-thumbs-up']) : '';
                    return $text;

                },
            ],
            [
                'attribute' => 'branch_id',
                'format'    => 'html',
                'value'     => function($data){ if($data->branch) return preg_replace('/日本ホメオパシーセンター|総?本部/u', '', $data->branch->name); },
                'filter'    => $branch,
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
                                   . Yii::$app->formatter->asDate($data->itv_date,'php: D');
                    }
                },
            ],
            [
                'attribute' => 'itv_time',
                'label'     => '時間',
                'format'    => 'html',
                'value'     => function($data)
                {
                    if($data->itv_time)
                    {
                        if ($data->itv_time === '00:00:00')
                            return null;
                        $start_time = date('H:i', strtotime($data->itv_time));
                        $end_time = date('H:i', strtotime("+{$data->duration} minutes", strtotime($data->itv_time)));
                        return "{$start_time}～{$end_time}";
                    }
                },
                'filter' => false,
                'contentOptions' => ['style' => 'width: 8%']
            ],
            [
                'attribute' => 'duration',
                'format'    => 'html',
                'filter'    => false
            ],
            [
                'attribute' => 'homoeopath_id',
                'format'    => 'raw',
                'value'     => function($data)
                {
                    if($data->homoeopath)
                        return ('app-backend' == Yii::$app->id) ? Html::a($data->homoeopath->homoeopathname, Url::to(["/sodan/homoeopath/{$data->homoeopath_id}"]), ['class'=>'btn-default']) : $data->homoeopath->homoeopathname;
                },
                'filter'    => (Yii::$app->id === 'app-backend') ? $this->homoeopath : false
            ],
            [
                'attribute' => 'client_id',
                'format'    => 'html',
                'value'     => function($data)
                {
                    if(! $data->client_id)
                        return '';

                    $client = \common\models\sodan\Client::findOne($data->client_id);
                    if (!$client) {
                        return '';
                    }
                    $html = '';
                    $icon = '';

                    if($client->isAnimal())
                        $icon = Html::img(Url::base().'/img/paw.png',['class'=>'icon','title'=>'動物相談です']);

                    elseif($client->customer->age && $client->customer->age < 13)
                        $icon = Html::tag('i','',['title'=>'子供です','style'=>'color:#FF33FF','class'=>'glyphicon glyphicon-user']);

                    if(! $data->hadMetBefore())
                        $html .= Html::tag('span','★',['style'=>'color:#eea236;','title'=>'初めての相談です']);

                    if(! $client->isValid())
                        $html .= Html::tag('span','',['class'=>'glyphicon glyphicon-alert text-danger','title'=>implode(';',$client->firstErrors)]);

                    if (!$client->ng_flg)
                        $html .= Html::tag('i', '', ['title' => '公開OKです', 'style' => 'color:#337ab7;font-size:1.2em;padding:2px;', 'class' => 'glyphicon glyphicon-thumbs-up']);

                    $html = $icon . Html::a($client->name, ['client/view','id' => $data->client_id], ['class'=>'btn-default']) . Html::a($html,['client/view','id' => $data->client_id]);
                    $html.= ((int)$data->pastitv <= 1) ? ' (初)' : " ({$data->pastitv})";

                    return $html;
                }
            ],
            [
                'attribute' => 'product_id',
                'value'     => function($data){ return $data->product ? $data->product->name : ($data->client_id ? null : ''); },
                'filter'    => $this->products,
            ],
            [
                'attribute' => 'status_id',
                'format'    => 'raw',
                'value'     => function($data)
                               {
                                   $text = $data->status->name . "<br>";
                                   if ($data->client_id && $data->status_id == InterviewStatus::PKEY_READY) {
                                       $text .= Html::a('印刷', [
                                                'interview/print',
                                                'id'     => $data->itv_id,
                                                'page'   => 'reserve',
                                                'format' => 'pdf'
                                           ],
                                           [
                                                'style'  => 'margin-left:10px;',
                                                'class'  => 'btn btn-xs btn-default',
                                                'target' => '_brank',
                                                'title'  => '予約票を印刷します'
                                           ]);
                                       $text .= Html::a('選択', [
                                                'interview/template-select',
                                                'id'     => $data->itv_id,
                                                'page'   => 'reserve',
                                                'format' => 'pdf'
                                           ],
                                           [
                                                'style'  => 'margin-left:10px;',
                                                'class'  => 'btn btn-xs btn-default',
                                                'title'  => '予約票に追加するテンプレートを選択してを印刷します'
                                           ]);
                                   }
                                   return $text;
                               },
                'filter'    => \yii\helpers\ArrayHelper::map(InterviewStatus::find()->select(['status_id','name'])->asArray()->all(), 'status_id', 'name'),
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
