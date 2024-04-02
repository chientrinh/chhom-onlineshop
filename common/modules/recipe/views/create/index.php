<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/recipe/views/create/index.php $
 * $Id: index.php 4023 2018-09-14 11:36:02Z kawai $
 *
 * $model common\models\RecipeForm
 * $enableClientChange boolean
 */

use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;
use \yii\bootstrap\ActiveForm;
use \common\modules\recipe\widgets\DropDown;

$this->params['body_id']       = 'Mypage';
$this->title = $title;
//$this->title = sprintf("新規作成 | 適用書 | %s", Yii::$app->name);
$csscode = "
.manual-input {
    float: left;
    margin: 0 10px;
}
input[type='text'][disabled]{
    background:#DCDCDC;
    cursor:not-allowed; /* 禁止カーソル */
}
";

$this->registerCss($csscode);

$jscode = "

// フォームの送信先
var url = $('#recipe-item-form').attr('action');
target = '';

submitForm = function(action) {
console.log(action);
    data = {};
    data['action'] = action;
    data['memo'] = '';
    data['instruct_id'] = '';
    data['quantity'] = '';
    data['target'] = target;

    $('select').each(function(){
        name = this.getAttribute(\"name\");
        console.log(\"this is \"+name);
        if(-1 != name.search( /instruct_id/ )){
            data['instruct_id'] += '&'+this.value;
        }
    });

    $('strong').each(function(){

        name = this.getAttribute(\"name\");
        if(-1 != name.indexOf('quantity[')){
             data['quantity'] += '&'+parseInt(this.innerText, 10);
        }
    });

    $('input').each(function(){

        name = this.getAttribute(\"name\");

        if(-1 != name.search( /memo\[/ )){
            console.log('target is '+this.value);
            //if(data['memo'].length == 0) {
            //    data['memo'] = this.value;
            //} else {
                data['memo'] += '&'+this.value;
            //}
        } else {
            if((name == 'manual_client_age' || name == 'manual_protector_age' || name == 'tel') && (this.value).length > 0) {
                data[name] = charactersChange(String(this.value));
            } else {
                data[name] = this.value;
            }
        }
    });

    $('textarea').each(function(){
          name = this.getAttribute(\"name\");
          if(name == 'note') {
              data['note'] = this.value;
          }
    });
console.log(data);
    $.ajax( {
        type: 'POST',
        url: url,
        data: data,
        dataType: 'json',
        cache : false,
        timeout: 30000,
        beforeSend: function ( jqXHR, settings ) {
            console.log(jqXHR);
            $('a').attr('disabled', true);
        },
        success: function( data ) {
            console.log(data);
            $('a').attr('disabled', false);
        },
        error: function( data ) {
           console.log('error');
           console.log(data);
           $('a').attr('disabled', false);
        }
    } );
}

var charactersChange = function(val){
    var han = val.replace(/[Ａ-Ｚａ-ｚ０-９：]/g,function(s){return String.fromCharCode(s.charCodeAt(0)-0xFEE0)});

    if(val.match(/[Ａ-Ｚａ-ｚ０-９：]/g)){
        return han;
    }
    return han;
}

incrementQty = function(val) {
    $('strong').each(function(){

        name = this.getAttribute(\"name\");
        if(-1 != name.indexOf('quantity['+val)){
            qty = parseInt(this.innerText);
            qty++;
            this.innerText = ' '+qty+' ';
            if(qty > 1) {
                this.setAttribute('class', 'alert-text alert-danger');
            } else {
                this.setAttribute('class', '');
            }

            return;
         }
    });
}

decrementQty = function(val) {
    $('strong').each(function(){

        name = this.getAttribute(\"name\");
        if(-1 != name.indexOf('quantity['+val)){
            qty = parseInt(this.innerText,10);
            if(qty > 0) {
                qty--;
                this.innerText = ' '+qty+' ';
                if(qty > 1 || qty == 0) {
                    this.setAttribute('class', 'alert-text alert-danger');
                    if(qty == 0) {
                        this.innerText = ' '+qty+' ';
                    }
                } else {
                    this.setAttribute('class', '');
                }
            }
            return;
         }
    });
}

incrementOffset = function(key) {
   console.log(key);
    $('a').each(function(){

        name = this.getAttribute(\"name\");
        if(-1 != name.indexOf('move-item['+key)){
//            seq = parseInt(this.getAttribute('value'),10);
//            console.log(seq);
//                seq++;
                target = key+'_'+1;
                return;
        }
    });
    return;
}

decrementOffset = function(key) {
   console.log(key);
    $('a').each(function(){

        name = this.getAttribute(\"name\");
        if(-1 != name.indexOf('move-item['+key)){
//            seq = parseInt(this.getAttribute('value'),10);
//            if(seq > 0) {
//                seq--;
                target = key+'_'+-1;
                return;
//            }
        }
    });
    return;
}

setDeleteItem = function(index) {
    $('a').each(function(){

        name = this.getAttribute(\"name\");
        if(-1 != name.indexOf('item['+index)){
             target = 'item_seq_'+index;
             return;
        }
    });
    return;
}

// クライアント情報を検索で入力した場合は、手入力フォームは無効にする
controlManualInput($('.search-client').text());

// クライアント（検索）での値が設定されていないかを判定し、手入力用フォームの有効無効を制御する
function controlManualInput(searchClient) {
// 編集機能の場合は、検索（再検索）ボタンがdisabledになっている。その場合は無条件にdisabledにする
    if($('#search-client').attr('disabled')) {
        $('.manual-input-client').prop('disabled', true);
        $('.manual-input-client').css('background', '#DCDCDC');
        return;
    }
    if (searchClient) {
        $('.manual-input-client').prop('disabled', true);
        $('.manual-input-client').css('background', '#DCDCDC');

    } else {
        $('.manual-input-client').prop('disabled', false);
        $('.manual-input-client').css('background', '#FFFFFF');
    }
}

//$('a').on('click', function(){
//    $('a').attr('disabled', true);
//});


";
$this->registerJs($jscode);
?>

<div class="cart-view">

  <div class="col-md-12">

  <?= $this->render('_tab', ['model' => $model]) ?>

      <h2><span>適用書 <?= sprintf($model->recipe_id ? "(No.".$model->recipe_id. "を編集中)" : "")?></span></h2>

<?php
$dataProvider = new \yii\data\ArrayDataProvider([
    'allModels' => $model->items,
    'pagination' => false,
]);
?>

<?php $form = ActiveForm::begin([
    'id'     => 'recipe-item-form',
    'method' => 'get',
    'action' => ['apply'],
    'fieldConfig' => [
    'template' => "{input}\n{hint}\n{error}",
],
]) ?>
<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'tableOptions' => ['class'=>'table table-condensed table-striped alert alert-success'],
    'layout'  => '{items}{summary}',
    'summary' => '<p class="text-right">計 <strong>{totalCount}</strong> 品目</p>',
    'emptyText' => 'レメディーが選択されていません',
    'columns' => [
        [
            'label' => '#',
            'format' => 'raw',
            'value' => function($data, $key, $index, $column) use ($model)
            {
                $seq = property_exists($data, 'seq') ? $data->seq : $key;

                $btnUp   = Html::a('&#x21bf;','javascript:void(0)',['name' => "move-item[$key]", 'value' => $seq, 'title'=>'一つ上に移動します', 'onClick' => "decrementOffset($key); submitForm('move-item')"]);
                $btnDown = Html::a('&#x21c2;','javascript:void(0)',['name' => "move-item[$key]", 'value' => $seq, 'title'=>'一つ下に移動します', 'onClick' => "incrementOffset($key); submitForm('move-item')"]);
/*['move-item','seq'=>$seq,'offset'=>-1]*/
                /*['move-item','seq'=>$seq,'offset'=>1]*/
                return ($seq ? $btnUp : Html::tag('span','&#x21bf',['style'=>'color:white']))
                     . Html::tag('span', $seq)
                     . (isset($model->items[$seq+1]) ? $btnDown : null);
            }
        ],
        [
            'attribute' => 'name',
            'label'     => '品名',
            'format' => 'raw',
            'value' => function($data, $key, $index, $column)
            {
                if(property_exists($data, 'remedy_id') && $data->remedy)
                    $label = $data->remedy->abbr;
                else
                    $label = nl2br($data->name);

                return Html::a('✖', 'javascript:void(0)',['name' => "item[$index]", 'class'=>'btn btn-xs text-muted pull-right','title'=>'削除します', 'onClick' => "setDeleteItem($index); submitForm('del-item')"])
                     . $label;
            }
        ],
        [
            'attribute'=>'quantity',
            'label'    => '個( 0＝削除 )',
            'format'   =>'raw',
            'value'    => function($data,$key,$index,$column)
            {
//                return Html::tag('strong', "quantity[$key]", $data->quantity, ['style'=>'border:none;outline:none;']);

                return Html::a('-','javascript:void(0)',['class'=>'badge', 'onClick' => "decrementQty($key)"])
                    .  Html::tag('strong',' '.$data->quantity.' ',[
                        'class'=> (1 < $data->quantity) ? 'alert-text alert-danger' : ''
                    ,'name' => "quantity[$key]"])
                    .  Html::a('+','javascript:void(0)',['class'=>'badge', 'onClick' => "incrementQty($key)"]);

            }
        ],
        [
            'attribute' => 'instruction',
            'label'    => '目安',
            'format'   =>'raw',
            'value'    => function($data,$key,$index,$column)
            {
               return Html::dropDownList("instruct_id[$key]", $data->instruct_id,ArrayHelper::map(\common\models\RecipeInstruction::find()->all(),'instruct_id','name'));
            }
        ],
        [
            'attribute' => 'memo',
            'label'    => 'メモ',
            'format'   =>'raw',
            'value'    => function($data,$key,$index,$column)
            {
                return Html::textInput("memo[$key]", $data->memo, ['class'=>'form-control memo','style'=>'border:none;outline:none;']);
            },
            'headerOptions' => ['class'=>'col-md-2 col-xs-1'],
        ],
    ],
])?>
<?php $form->end() ?>

<?php $this->registerjs("$(function() {
    $( document ).tooltip();
  });"); ?>
<div class="form-group">
    <?= Html::a('追加','javascript:void(0)',['class'=>'btn btn-success',  'onClick' => 'submitForm("compose")'])?>
    <!-- <?= Html::a('すべて削除', ['del','target'=>'all'],['class'=>'btn btn-default'])?> -->
    <span class="pull-right">
    <?= Html::a('変更を適用', 'javascript:void(0)',['class'=>'btn btn-success', 'onClick' => 'submitForm("index")'])?>
    <?= Html::a('プレビュー', 'javascript:void(0)',['class'=>'btn btn-info', 'onClick' => 'submitForm("view")'])?>
    <?= Html::a('適用書作成完了', 'javascript:void(0)',[
        'class'=>'btn btn-danger',
        'onClick' => 'submitForm("finish")',
        'title'=>'このボタンを押すまで、作成中の内容は24時間サーバに保存されます。ふたたびログインすると作成を再開できます。',
    ])?>
    </span>
</div>

      <?php if($model->hasErrors()): ?>
       <?= Html::errorSummary($model,['class'=>'alert alert-danger']) ?>
      <?php endif ?>

<?php $form = ActiveForm::begin([
    'id'     => 'recipe-form',
    'method' => 'get',
    'action' => ['apply'],
    'fieldConfig' => [
    'template' => "{input}\n{hint}\n{error}",
],
]) ?>
<?= \yii\widgets\DetailView::widget([
    'model' => $model,
    'options' => ['class'=>'table table-condensed'],
    'attributes' => [
        [
            'attribute' => 'note',
            'format'    => 'raw',
            'value'     => $form->field($model,'note')->textArea(['name'=>'note','rows'=>7]),
        ],
        [
            'attribute' => 'client_id',
            'label'     => 'クライアント<br>（モール会員検索）',
            'format'    => 'raw',
            'value'     =>  Html::tag('i',
                            (($c = $model->client) ?
                                $c->name. '　 '.Html::a(
                                        '削除',
                                        'javascript:void(0)',
                                        ['class'=>'btn btn-danger', 'onClick'=>'submitForm("search-client-delete")', 'disabled' => !$model->isNewRecord ? true : false, 'title'=>'クライアントをリセットします']) : null
//                                        ['del', 'target'=>'search-client-delete'],
                                ),
                                ['class'=>'search-client']
                            )
                         . '　 '
                         . Html::a($c ? '再検索' : '検索','javascript:void(0)',
                                 ['id'=>'search-client','class'=>'btn '. ($c ? 'btn-default':'btn-success'),'title'=>'クライアントを検索する', 'disabled' => !$model->isNewRecord ? true : false, 'onClick'=>'submitForm("search-client")']),
                                 // ['search','target'=>'client'],
            'visible'   => true,
        ],
        [
            'label'     => 'クライアント（手入力）',
            'format'    => 'raw',
            'value'     => $form->field($model, 'manual_client_name')
                                ->textInput([
                                    'name' => 'manual_client_name',
                                    'maxlength' => 50,
                                    'class' => 'input-sm manual-input manual-input-client',
                                ]).
                            $form->field($model, 'manual_client_age')
                                ->textInput([
                                    'name' => 'manual_client_age',
                                    'maxlength' => 3,
                                    'class' => 'input-sm manual-input manual-input-client js-zenkaku-to-hankaku',
                                    'size' => '1'
                                ]).
                            "歳",
        ],
        [
            'label'     => '保護者（手入力）',
            'format'    => 'raw',
            'value'     => $form->field($model, 'manual_protector_name')
                                ->textInput([
                                    'name' => 'manual_protector_name',
                                    'maxlength' => 50,
                                    'class' => 'input-sm manual-input manual-input-client',
                                ]).
                            $form->field($model, 'manual_protector_age')
                                ->textInput([
                                    'name' => 'manual_protector_age',
                                    'maxlength' => 3,
                                    'class' => 'input-sm manual-input manual-input-client js-zenkaku-to-hankaku',
                                    'size' => '1'
                                ]).
                            " 歳",
        ],
        [
            'attribute' => 'center',
            'label'     => 'センター名',
            'format'    => 'raw',
            'value'     => $form->field($model, 'center')
                                ->textInput([
                                    'name' => 'center',
                                    'maxlength' => 50,
                                    'size'=>'60',
                                    'class' => 'input-sm manual-input',
                                ]),
        ],
        [
            'attribute' => 'tel',
            'label'     => '電話番号',
            'format'    => 'raw',
            'value'     => $form->field($model, 'tel')
                                ->textInput([
                                    'name' => 'tel',
                                    'maxlength' => 11,
                                    'size'=>'60',
                                    'class' => 'input-sm manual-input js-zenkaku-to-hankaku',
                                ]).
                            "<p class='help-block'>半角数字のみ（ハイフン無し）で入力して下さい。</p>",
        ],
        [
            'attribute' => 'homoeopath_id',
            'format'    => 'text',
            'value'     => ($h = $model->homoeopath) ? $h->homoeopathname : null,
        ],
        [
            'attribute' => 'expire_date',
            'format'    => ['date', 'php:Y-m-d D'],
        ],
    ],
]) ?>
<?php $form->end() ?>

  </div>

</div>
