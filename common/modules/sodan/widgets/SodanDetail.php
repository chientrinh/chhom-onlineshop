<?php
namespace common\modules\sodan\widgets;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/widgets/SodanDetail.php $
 * $Id: SodanDetail.php 3851 2018-04-24 09:07:27Z mori $
 */

use Yii;
use \yii\helpers\Html;

class SodanDetail extends \yii\base\Widget
{
    /* @var Interview model */
    public $model;

    /* @var array of string */
    public $attributes = [];

    /* @var array of ViewItems */
    public $columns = [];

    public function init()
    {
        parent::init();

        foreach($this->getAllAttributes() as $params)
        {
            $key = $params['attribute'];

            if(in_array($key, $this->attributes))
                $this->columns[] = $params;
        }
    }

    public function run()
    {
        echo \yii\widgets\DetailView::widget([
            'model'      => $this->model,
            'attributes' => $this->columns,
        ]);
    }

    private function getAllAttributes()
    {
        $btnPurchase = null;
        $btnCancel   = null;
        $model = $this->model;

        if(($user = Yii::$app->user->identity) && $user instanceof \backend\models\Staff)
        {
            if($model->client)
                $btnPurchase = Html::a('起票',['purchase/create','id'=>$model->itv_id],['class'=>'pull-right btn btn-xs btn-default']);

            if(\common\models\sodan\InterviewStatus::PKEY_READY == $model->status_id)
                $btnCancel = Html::a('予約をキャンセルする',
                                     ['interview/cancelate','id'=>$model->itv_id],
                                     ['class'=>'pull-right btn btn-xs btn-default']);
        }

        return [
            [
                'attribute' => 'itv_date',
                'format'    => 'html',
                'value'     => Yii::$app->formatter->asDate($model->itv_date,'php:Y-m-d (D) ')
                             . Yii::$app->formatter->asTime($model->itv_time,'php:H:i')
                             . Html::tag('span',$model->branch->name,['class'=>'pull-right']),
            ],
            [
                'attribute' => 'homoeopath_id',
                'value'     => ($hpath = $model->homoeopath) ? $hpath->homoeopathname : null,
            ],
            [
                'attribute' => 'client_id',
                'format'    => 'html',
                'value'     => ($model->client
                              ? Html::a($model->client->name, ['client/view','id'=>$model->client_id])
                              : $fmt->asText(null)) . '&nbsp;' .
                             (($model->client && ($client = \common\models\sodan\Client::findOne($model->client_id)) && ! $client->isValid())
                                 ? Html::tag('span',implode('; ',$client->firstErrors),['class'=>'text-warning'])
                                     : ''),
            ],
            [
                'attribute' => 'product_id',
                'value'     => $model->product ? $model->product->name : null,
            ],
            [
                'attribute' => 'status_id',
                'format'    => 'raw',
                'value'     => sprintf('%s %s', $model->status->name, $btnCancel),
            ],
            [
                'attribute' => 'purchase_id',
                'format'    => 'raw',
                'value'     => $model->purchase
                             ? Html::a(sprintf('%06d', $model->purchase_id), ['/purchase/view','id'=>$model->purchase_id])
                             : '記録なし' . $btnPurchase,
            ],
        ];
    }

}

