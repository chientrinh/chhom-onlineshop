<?php

namespace common\widgets;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/ComplexRemedyView.php $
 * $Id: ComplexRemedyView.php 3914 2018-06-01 07:09:56Z mori $
 */
use Yii;
use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;

use \backend\models\Staff;
use \common\models\Company;
use \common\models\Customer;
use \common\models\ProductMaster;
use \common\models\Remedy;
use \common\models\RemedyPotency;
use \common\models\RemedyStock;
use \common\models\RemedyVial;
use \common\models\RemedyPriceRangeItem;

class ComplexRemedyView extends \yii\base\Widget
{
    /* @var \common\models\Customer | \backend\models\Staff */
    public $user;

    /* @var \common\components\cart\ComplexRemedyForm */
    public $model;

    /* @var boolean */
    public $showPrice = true;

    /* @var array of [remedy_id => Remedy::abbr] */
    private $_abbrs;

    /* @var cache life cycle */
    private $duration = 86400; // 24 hour

    private $dependency = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->dependency = new \yii\caching\DbDependency([
            'sql'     => 'SELECT MAX(update_date) FROM mtb_remedy_stock',
            'reusable'=> true,
        ]);
    }

    public function getAbbrs()
    {
        if($this->_abbrs)
            return $this->_abbrs;

        $user = $this->user;
        $cache_id = "remedystock-droppables-abbr-" . ($user instanceof Staff ? null : ($user ? $user->grade_id : 0));
//        if(! $abbrs = Yii::$app->cache->get($cache_id))
//        {
            $query = RemedyStock::find();
            $query->andWhere(['vial_id'  => RemedyVial::DROP,
                              'in_stock' => 1 ])
                  ->select('remedy_id')
                  ->distinct(true)
                  ->with('remedy');
            $query->from('mvtb_product_master');
            $query->andWhere(['not', ['name' => '']]);
            if(! $user instanceof Staff)
                $query->forcustomer($this->user);

            $abbrs = $query->all();
            $abbrs = ArrayHelper::getColumn($abbrs, 'remedy.abbr');
            sort($abbrs); // 昇順

//            Yii::$app->cache->set($cache_id, $abbrs, $this->duration, $this->dependency);
//        }
        $this->_abbrs = $abbrs;

        return $this->_abbrs;
    }

    public function run()
    {
        echo '<div class="row">';

        echo '<div class="col-md-9 col-sm-6 col-xs-12">';
        $this->renderVial();
        $this->renderDrops();
        echo '</div>';

        echo '<div class="col-md-3 col-sm-6 col-xs-12">';
        $this->renderSummary();
        $this->renderHelperMenu();
        echo '</div>';

        $this->renderHelperBody();

        echo '</div>';
    }

    private function renderHelperBody()
    {
        // jQuery for dynamic show()/hide() of Remedy::abbr
        $jscode = "

// タブ「オリジナルMT」を読み込み時に絞込を行う
setMtFilter($('#mt-filter input:checked'));

$('a').click(function () {

  if(! $(this).attr('rel') ) return true;

  $('.jumbotron').html( $('#' + $(this).attr('rel')).html() )
      return false;
});

$('.jumbotron').on('click','span',function() {

 abbr = $(this).attr('value');
 idx  = ".(count($this->model->drops)-1).";

 input = $('#remedy-abbr-'+idx);
 input.val(abbr);

 btn   = $('#remedy-btn-'+idx);
 btn.addClass('btn-success');

 $('.Detail-Total :button[type=submit]').addClass('disabled');
});

// タブ「オリジナルMT」のラジオボタン選択時に絞込を行う
$('#mt-filter').on('click','input',function()
{
  setMtFilter($(this));
});


function setMtFilter(obj)
{
  var re = new RegExp( obj.val() );

  $('#vial-barcode option').each(function(index, value)
  {
    var val = $(this).text();

    if(re.test(val)) // show element
    {
        if(navigator.appName == 'Microsoft Internet Explorer') {
            if (this.nodeName.toUpperCase() === 'OPTION') {
                var span = $(this).parent();
                var opt  = this;
                if($(this).parent().is('span')) {
                    $(opt).show();
                    $(span).replaceWith(opt);
                }
            }
        }
        else
            $(this).show(); // all other browsers use standard .show()
                            // see http://ajax911.com/hide-options-selecbox-jquery/
    }
    else // hide element
    {
        if ($(this).is('option') && (!$(this).parent().is('span')))
            $(this).wrap((navigator.appName == 'Microsoft Internet Explorer') ? '<span>' : null).hide();
    }
  });

  return true;
}

// オリジナルMTフィルタ
var optionArray = new Array();
$('#vial-barcode').children().each(function(){
   optionArray.push( { value:$(this).val(), body:$(this).html() });
});

$('#search-filter').keyup(function(){
    var s = $(this).val();
    $('#vial-barcode').empty();
    if (s == ''){
       $(optionArray).each(function(i, o){
          $('#vial-barcode').append( $('<option>').val(o.value).text(o.body) );
       });
    } else {
       optionArray.filter(function(o, i){
          if (o.body.toLowerCase().indexOf(s.toLowerCase()) != -1){
             $('#vial-barcode').append( $('<option>').val(o.value).text(o.body) );
          }
       });
    }
});
";
    $this->view->registerJs($jscode);

echo <<<DIV
  <div class="jumbotron col-md-12 col-sm-12 col-xs-12"><!-- placeholder for Remedy::abbr -->
  </div>
<div id="abbr" style="display:none; white-space: nowrap"><!-- jQuery table of contents, storing Remedy::abbr -->
DIV;

        foreach(str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ') as $letter)
        {
            $abbrs = array_filter($this->abbrs, function($value)use($letter){ return $letter == $value[0]; });

            echo "<div id='$letter'>";

            foreach($abbrs as $abbr)
            {
                $label   = preg_replace('/-/', '&#8209;', $abbr);
                echo Html::tag('span',$label,['class'=>'btn-xs btn-default','value'=>$abbr]), ' ';
            }
            echo'</div>';
        }
        echo'</div>';
    }

    private function renderHelperMenu()
    {

        foreach(str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ') as $letter)
        {
            $items[] = Html::a($letter, '#', ['rel' => $letter]);
        }

        echo <<<DIV
    <div id="Search">
      <div class="Mypage-Nav product-search">
        <div class="inner">
DIV;

        echo Html::ul($items,['class'=>'initial', 'encode'=>false]);

echo <<<DIV
        </div>
      </div>
    </div>
DIV;
    }

    private function renderDrops()
    {
        $model = $this->model;

        echo \yii\widgets\ListView::widget([
            'dataProvider'  => new \yii\data\ArrayDataProvider([
                'allModels'  => $model->drops,
                'sort'       => false,
                'pagination' => false,
            ]),
            'layout'        => '{items}',
            'itemView'      => '@common/widgets/views/complex-remedy-drop',
            'viewParams'    => [
                'remedies' => $this->abbrs,
                'user'     => $this->user,
            ],
        ]);

        if(count($model->drops) < $model->maxDropLimit)
        echo '<div class="col-md-12 text-right">',
 Html::submitButton(" + ", [
             'class' => 'btn btn-success',
             'name'  => 'command',
             'value' => 'extend',
             'title' => sprintf('滴下 %d を追加します', count($model->drops)+1),
         ]),
        '</div>';

        echo '<div class="col-md-12">',
        Html::a("↩", Yii::$app->request->referrer, ['class'=>'btn btn-default btn-xs','title'=>"一つ前にもどす"]),
        '</div>';
    }

    private function getMtCandidates()
    {
        //$cache_id  = 'remedystock-mt-candidates';

        //if(! $vials  = Yii::$app->cache->get($cache_id))
        {
            $query = RemedyStock::find();
            $query->orWhere(['vial_id' => RemedyVial::GLASS_20ML]);
            $query->orWhere(['vial_id' => RemedyVial::PLASTIC_SPRAY_20ML]);

            // バックヤードのみで使用するMTを直接取得
            if (Yii::$app->id === 'app-backend') {
                $query->orWhere(['in', 'vial_id', [RemedyVial::ORIGINAL_20ML, RemedyVial::ORIGINAL_150ML]]);
            }

            if (Yii::$app->id === 'app-frontend') {
                $query->andWhere(['not', ['restrict_id' => '99']]);
            }

            $vials = ArrayHelper::map($query->all(), 'sku_id', function($model){
                $name = ProductMaster::find()->where([
                    'remedy_id' => $model->remedy_id,
                    'potency_id'=> $model->potency_id,
                    'vial_id'   => $model->vial_id,
                ])
                                             ->select('name')
                                             ->scalar();

                $name = $name ? $name : ArrayHelper::getValue($model,'remedy.abbr');

                if(RemedyPotency::MT == $model->potency_id || RemedyPotency::JM == $model->potency_id)
                    $name .= ' '; // 単体MTには見えない印を末尾につけておく

                return $name;
            });
            asort($vials); // 昇順
          //  Yii::$app->cache->set($cache_id, $vials, $this->duration, $this->dependency);
        }
        return $vials;
    }

    private function getVialCandidates()
    {
        $cache_id  = 'remedyprange-vial-candidates';

// RemedyStockのレコード更新に依存するキャッシュが設定されている。変更をかけたらRemedyStockのupdate_dateが更新されるようにすること
//        if(! $vials  = Yii::$app->cache->get($cache_id))
//        {
        $vial_list = [RemedyVial::SMALL_BOTTLE,
                      RemedyVial::LARGE_BOTTLE,
                      RemedyVial::GLASS_5ML,];
        if (Yii::$app->id === 'app-backend') {
            $vial_list = array_merge($vial_list, [RemedyVial::ALP_20ML]);
        }

        $query = RemedyPriceRangeItem::find();
        $query->andWhere(['vial_id'  => $vial_list,
                          'prange_id'=> 8 ])
              ->select('vial_id')
              ->distinct(true)
              ->with('vial');

        $vials = ArrayHelper::map($query->all(), 'vial_id', 'vial.name');
        ksort($vials); // 降順

//            Yii::$app->cache->set($cache_id, $vials, $this->duration, $this->dependency);
//        }

        return $vials;
    }

    private function renderVial()
    {
        $model = $this->model;

        $vials   = $this->getVialCandidates();
        // アルポ5mlと20mlの間に空行をいれる
        $this->array_insert($vials, ['' => ''], 3);
        $bottles = $this->getMtCandidates();

        $listbox1 = [
            'label'   => 'オリジナル',
            'active'  => in_array($model->vial->vial_id, array_keys($vials)),
            'content' => Html::activeListbox($model->vial, 'vial_id', $vials, [
                'name'    => 'Vial[vial_id]',
                'onClick' => 'document.getElementById("vial-barcode").value=null; this.form.submit()',
                'id'      => 'vial-id',
            ])         . Html::tag('span','容器を選んでください',['class'=>"text-muted"])
        ];
        if($bottles)
        $listbox2 = [
            'label'   => 'オリジナルMT',
            'active'  => in_array($model->vial->sku_id, array_keys($bottles)),
            'content' =>
              Html::textInput('seach-filter', '', [
                'class' => 'form-control',
                'id' => 'search-filter',
                'autocompete' => 'off',
                'style' => 'width:40%;margin:5px 0;margin-left:50%;'
            ])
            . Html::activeListbox($model->vial, 'sku_id', $bottles, [
                'name'    => 'Vial[barcode]',
                'onClick' => 'document.getElementById("vial-id").value=null; this.form.submit()',
                'id'      => 'vial-barcode',
                'size'    => 7,
                'class'   => 'text-info col-md-6',
            ])
            . Html::radioList('mt', Yii::$app->request->get('mt'), [
                                            'Can'          => 'Can',
                                            'Pet'          => 'Pet',
                                            'Thuj'         => 'Thuj',
                                            'サポート'      => 'サポート',
                                            'JM\)'           => 'JM',
                                            '.+'           => 'すべて',
               ],[
                   'id'        => 'mt-filter',
                   'separator' => '<br>',
                   'class'     => "text-muted col-md-6"])
              . Html::tag('span','母体とするチンクチャーを選んでください',['class'=>"text-muted col-md-6"])
        ];

        if(! isset($listbox2))
            echo '<ul class="nav nav-tabs"><li class="active">容器</li></ul>',
                 $listbox1['content'];
        else
            echo \yii\bootstrap\Tabs::widget([
                'items'         => [$listbox1, $listbox2],
                'clientOptions' => ['collapsible' => false],
            ]);
    }

    /**
    * 配列（連想配列にも対応）の指定位置に要素（配列にも対応）を挿入して、挿入後の配列を返す
    *
    * @param array &$base_array 挿入したい配列
    * @param mixed $insert_value 挿入する値（文字列、数値、配列のいずれか）
    * @param int $position 挿入位置（省略可能。先頭は0、省略時は配列末尾に挿入される）
    * @return boolean 挿入成功時にtrue
    **/
    private function array_insert(&$base_array, $insert_value, $position=null) {
        if (!is_array($base_array))
            return false;
        $position = is_null($position) ? count($base_array) : intval($position);
        $base_keys = array_keys($base_array);
        $base_values = array_values($base_array);
        if (is_array($insert_value)) {
            $insert_keys = array_keys($insert_value);
            $insert_values = array_values($insert_value);
        } else {
            $insert_keys = array(0);
            $insert_values = array($insert_value);
        }
        $insert_keys_after = array_splice($base_keys, $position);
        $insert_values_after = array_splice($base_values, $position);
        foreach ($insert_keys as $insert_keys_value) {
            array_push($base_keys, $insert_keys_value);
        }
        foreach ($insert_values as $insert_values_value) {
            array_push($base_values, $insert_values_value);
        }
        $base_keys = array_merge($base_keys, $insert_keys_after);
        $is_key_numric = true;
        foreach ($base_keys as $key_value) {
            if (!is_integer($key_value)) {
                $is_key_numric = false;
                break;
            }
        }
        $base_values = array_merge($base_values, $insert_values_after);
        if ($is_key_numric) {
            $base_array = $base_values;
        } else {
            $base_array = array_combine($base_keys, $base_values);
        }
        return true;
    }

    public function renderSummary()
    {
        $model = $this->model;

echo <<<DIV
    <div id="Cart">
      <div class="Detail-Total">
        <div class="inner">
DIV;

echo $model->vial->name;

if($model->drops)
echo \yii\widgets\ListView::widget([
            'dataProvider'  => new \yii\data\ArrayDataProvider([
                'allModels'  => $model->drops,
                'sort'       => false,
                'pagination' => false,
            ]),
            'layout'        => '{items}',
            'itemView'      => function ($drop, $key, $index, $widget) {
          return sprintf('<hr><h5><span style="">%s</span> &nbsp;&nbsp;  <span>%s</span><span class="dropdown-menu-right">%s</span></h5>',
                  $drop->remedy ? $drop->remedy->name : '',
                  $drop->potency? $drop->potency->name : '',
                  Html::submitButton("×", [
                    'class'=> 'btn',
                    'name' => sprintf('Drops[%d][delete]',$index),
                    'title' => '削除',
                  ]));
          },
        ]);

        echo '<p class="text-right">',
        $this->showPrice ? Html::tag('strong', Yii::$app->formatter->asCurrency($model->price)) : '&nbsp;',
        '</p>';

        if(! $model->hasErrors())
        {
            echo <<<DIV
          <p class="text-center">
            <span class="detail-view-btn">
DIV;
            echo Html::submitButton("確定", [
                'class' => 'btn btn-warning',
                'name'  => 'command',
                'value' => 'finish',
            ]);
            echo <<<DIV
            </span>
          </p>
DIV;
        }
        else
        {
            echo <<<DIV
          <div class="alert alert-warning">
DIV;
            echo          Html::error($model, 'vial');
            echo $model->drops ? Html::error($model, 'drops') : '';
            echo Html::error($model, 'maxDropLimit');
            echo '</div>';

        }

        echo <<<DIV
</div>
</div>
</div>
DIV;

    }

}
