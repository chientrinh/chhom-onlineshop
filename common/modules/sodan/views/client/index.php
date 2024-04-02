<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/client/index.php $
 * $Id: index.php 4133 2019-03-28 04:52:50Z kawai $
 *
 * $this \yii\web\View
 * $dataProvider ActiveDataProvider
 * $searchModel  \common\models\SearchMember
 */

use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\helpers\ArrayHelper;
use \common\models\Membership;
use \common\models\sodan\Homoeopath;
use common\models\Branch;

$query = Membership::find()
        ->where(['membership_id' => [ Membership::PKEY_TORANOKO_GENERIC,
                                      Membership::PKEY_TORANOKO_NETWORK,
                                      Membership::PKEY_TORANOKO_GENERIC_UK,
                                      Membership::PKEY_TORANOKO_NETWORK_UK,
                                      Membership::PKEY_TORANOKO_FAMILY]]);
$mships = ArrayHelper::map($query->asArray()->all(), 'membership_id', 'name');

$query  = Homoeopath::find()->active();
if (isset($searchModel->branch_id) && $branch_id = $searchModel->branch_id) {
    $query->multibranch($branch_id);
}
$homoeopathIds = ArrayHelper::getColumn($query->asArray()->all(), 'homoeopath_id');

$customers = \common\models\Customer::find()->active()->where(['customer_id' => $homoeopathIds])->select(['customer_id', 'name01','name02','homoeopath_name'])->asArray()->all();

#$hpaths = ArrayHelper::map($query->all(), 'homoeopath_id', 'customer.homoeopathname');
$hpaths = ArrayHelper::map($customers, 'customer_id', 
    function($data) {
         if($data['homoeopath_name']) {
             return $data['homoeopath_name'];
         } else {
             return $data['name01'].' '.$data['name02'];
         }
    });
$query  = Branch::find()->center();
$branches = ArrayHelper::map($query->asArray()->all(),'branch_id','name');

$title = '一覧';
$labels = ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
krsort($labels);

$this->title = $title .' | ' . implode(' | ', $labels) . ' | '. Yii::$app->name;
?>

<h1>クライアント</h1>
<p class="help-block">
    本部相談会クライアントを一覧表示しています
    <br>
    ※灰色のクライアントは無効顧客となっているため、有効化を行ってください。
</p>

<p class="pull-left">
    <?= Html::a('すべて表示', Url::current(['client' => null]), ['class' => 'btn btn-default']) ?>
</p>
<p class="pull-left">
    <?= Html::a('公開OK', Url::current(['client' => 'open']), ['class' => 'btn btn-default', 'style' => 'margin-left:10px;', 'title' => '公開OKのクライアントを一覧で表示します']) ?>
</p>
<p class="pull-left">
    <?= Html::a('公開NG', Url::current(['client' => 'close']), ['class' => 'btn btn-default', 'style' => 'margin-left:10px;', 'title' => '公開NGのクライアントを一覧で表示します']) ?>
</p>

<?php if('app-backend' == Yii::$app->id): ?>
    <p class="pull-right">
        <?= Html::a('CSV表示', Url::current(['format'=>'csv']), ['class'=>'btn btn-default']) ?>
    </p>
    <p class="pull-right">
        <?= Html::a('追加', ['create'], ['class' => 'btn btn-success', 'style' => 'margin-right:10px;']) ?>
    </p>
<?php endif ?>
<p style="clear: both;"></p>
<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'rowOptions' => function($model) {
        if($model->customer->isExpired())
            return ['style' => 'background:#cccccc;'];
    },
    'columns' => [
        [
            'attribute'=> 'client_id',
            'format'   => 'html',
            'value'    => function($data)
            {
                $label = sprintf('%06d', $data->client_id);
                return Html::a($label, ['view','id' => $data->client_id, 'target' => 'client']);
            },
        ],
        'kana',
        [
            'attribute' => 'name',
            'format'    => 'html',
            'value'     => function($data){
                $icon = '';
                if($data->isAnimal())
                    $icon = Html::img(Url::base() . '/img/paw.png', ['class' => 'icon', 'title' => '動物です']);
                elseif($data->customer->age && $data->customer->age < 13)
                    $icon = Html::tag('i', '', ['title' => '子供です', 'style' => 'color:#FF33FF', 'class' => 'glyphicon glyphicon-user']);

                $ng_icon = (!$data->ng_flg) ? Html::tag('i', '', ['title' => '公開OKです', 'style' => 'color:#337ab7;font-size:1.2em;', 'class' => 'glyphicon glyphicon-thumbs-up']) : '';

                return "{$icon} {$data->name} {$ng_icon}";
            }
        ],
        [
            'attribute' => 'birth',
            'label'     => '生年月日',
            'format'    => 'html',
            'value'     => function($data){
                $customer = \common\models\Customer::findOne($data->client_id);
                return ($customer && $customer->birth) ? date('Y年m月d日', strtotime($customer->birth)) . "\n({$customer->age}才)" : null;
            },
            'filter'    => false
        ],
        [
            'attribute' => 'parent_name',
            'format'    => 'html',
            'value'     => function($data){
                $parent = $data->customer->getParent()->asArray()->one();
                if(isset($parent))
                    return $parent['name01'].' '.$parent['name02'];
                
                return $data->parent_name ? $data->parent_name : null;
            },
            'filter'    => false
        ],
        [
            'attribute' => 'branch_id',
            'format'    => 'html',
            'value'     => function($data, $key, $index, $column){ return ($data->branch && isset($column->filter[$data->branch_id])) ? $column->filter[$data->branch_id] : ''; },
            'filter'    => $branches
        ],
        'skype',
        [
            'attribute'=>'agreement',
            'value'    => function($data){ return $data->agreement ? 'はい' : null; },
        ],
        [
            'attribute'=>'questionnaires',
            'value'    => function($data){ return $data->questionnaires ? 'はい' : null; },
        ],
        [
            'label'    => '最近の相談会',
            'value'    => function($data)
            {
                $itv = $data->getInterviews()
                          ->active()
                          ->andWhere('itv_date <= NOW()')
                          ->orderBy(['itv_date'=> SORT_DESC, 'itv_time' => SORT_DESC ])->asArray()->one();

                if(!isset($itv))
                    return null;
                return $itv['itv_date'];
            }
        ],
        [
            'attribute'=> 'homoeopath_id',
            'format' => 'raw',
            'value'  => function($data, $key, $index, $column) {
                return ($data->homoeopath_id && isset($column->filter[$data->homoeopath_id])) ? Html::a($column->filter[$data->homoeopath_id], ['homoeopath/view', 'id' => $data->homoeopath_id]) : null;
            },
            'filter' => (Yii::$app->id === 'app-backend') ? $hpaths : null,
            'visible' => Yii::$app->id === 'app-backend'
        ],
        [
            'attribute' => '',
            'format'    => 'html',
            'value'     => function($data)
            {
                return (!$data->customer->isExpired()) ? Html::a('予約',['book','id' => $data->client_id],['class'=>'btn btn-xs btn-primary']) : '';
            },
            'visible' => Yii::$app->id === 'app-backend'
        ],
    ],
]) ?>
