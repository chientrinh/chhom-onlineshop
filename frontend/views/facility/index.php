<?php

use yii\helpers\Html;
use \yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use common\models\Facility;
use common\models\Membership;
use common\models\CustomerMembership;
use common\models\Pref;
use yii\bootstrap\Dropdown;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/facility/index.php $
 * $Id: index.php 4222 2020-01-14 02:55:26Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Facility
 * @var $query yii\db\ActiveQuery
 */
    
$this->params['body_id'] = 'Search';

if(is_scalar($pref_id) && ($pref = Pref::findOne($pref_id)))
    $this->params['breadcrumbs'][] = ['label' => $pref->name, 'url'=>['index','id'=>$pref_id]];


$css = '
td label{
    font-weight: normal;
}
h2 {
    margin: 0 0 20px;
    padding: 0;
    background-color: #F0F0F0;

    border: 1px solid #DDD;
    border-radius: 4px;
    -moz-border-radius: 4px;
    -webkit-border-radius: 4px;
}
h2 span {
    display: block;
    margin: 0 1px;
    padding: 12px 8px 10px;
    line-height: 1.2em;

    font-size: 16px;
    font-weight: bold;
    border-top: 1px solid #FFF;
}


.modal-backdrop {
  position: fixed;
  z-index: 1031;
  top: 0;
  bottom: 0;
  right: 0;
  left: 0;
  background-color: #000;


';// tak-zone.net 大石さんの流儀を踏襲
// Modal用のフェードについてCSSをオーバーライド
$this->registerCss($css);


// Modalを表示するタイミングでフェード用タグの前に移動させる
$js = '

    function alignModal(){
        var modalDialog = $("#detailModal").find(".modal-dialog");
        /* Applying the top margin on modal dialog to align it vertically center */
        modalDialog.css("margin-top", Math.max(0, ($(window).height() - modalDialog.height()) / 2));
    }
    
    // Align modal when user resize the window
    $(window).on("resize", function(){
        $(".modal:visible").each(alignModal);

    });   

function move_modal() {
    var modal = $("#detailModal");
    $("#detailModal").remove();
    modal.insertBefore($(".modal-backdrop"));
    
}

$(".modal").on("show.bs.modal", move_modal);
$(".modal").on("shown.bs.modal", alignModal);

$("area").on("click", function(e)
{
   $("#uQ2adoLB").attr("action", $(e.target).attr("href"));
   $("#uQ2adoLB").submit();
   return false;
});


// checkを入れた項目にalert-infoをすぐに反映
$("input[type=\'checkbox\']").on("click", function(e) {
    if(e.target.checked != true) {
        if(e.target.parentElement.parentElement.hasAttribute("class")) {
            e.target.parentElement.parentElement.removeAttribute("class");
        }
    } else {
        e.target.parentElement.parentElement.setAttribute("class","alert-info");
    }
});


// 地域セレクトボックスで選択した地域（pref_id）を元に遷移先を決定して遷移
$("#pref_select").change(function(){
// 選択されているoption要素を取得する
    var selectedItem = this.options[ this.selectedIndex ];
    var pref_id = selectedItem.value;
    var url = "index?id="+pref_id;
    if(pref_id.length == 0 || pref_id == 0) {
        $(this).attr("action", "index");
    } else {
        $(this).attr("action", "index?id="+pref_id);
    }
    //location.href=url;
    $(this).submit();
    return false;
});


$("html,body").animate({scrollTop:$("#search_result").offset().top});

';

$this->registerJs($js);

$prefs = ArrayHelper::map(Pref::find()->asArray()->all(), 'pref_id', 'name');
$prefs = array_merge(['0' => '全地域'], $prefs);

?>
<h1 class="mainTitle">
    提携施設
</h1>




<hr>

<?php $form = \yii\bootstrap\ActiveForm::begin([
    'id' => 'uQ2adoLB', // 絶対に重複しないIDを指定
    'enableClientValidation' => false,
]) ?>

<div class="row">

    <div class="col-md-4 col-sm-4" ><!-- 日本地図 -->

        <p>
            <label class="control-label" for="facility-name"><?= $model->getAttributeLabel('name')."で探す" ?></label>
        </p>

        <?= $form->field($model, 'name' ,['options'=>['class'=>'pull-left']])->label(false)->textInput() ?>
        <?= Html::submitButton('検索',['class'=>'btn btn-sm btn-default pull-left']) ?>

        <?php //$this->render('_map') ?>
    </div>
    <div class="col-md-4 col-sm-4" >
        <p>
            <label  class="control-label" >地域で探す</label>
        </p>

        <?= Html::dropDownList('pref',$pref_id, $prefs,['id'=>'pref_select','class'=>'pull-left form-control','style'=>'font-size:15px;','prompt' => '地域を選択']) ?><br /><br />
        <p class="help-block"><strong>検索したい地域をセレクトボックスで選択してください</strong></p>
    </div>


    <div class="col-md-4 col-sm-4 col-xs-12"><!-- チェックボックス -->
    <?php
        // 施設の種類を配列で定義, ORDER BY FIELDにも使用する
        // 参考: http://stackoverflow.com/questions/28856562/order-by-field-in-yii2
        // https://gendosu.jp/archives/1210
    
        $membership_array = [
            Membership::PKEY_HOMOEOPATHY_CENTER,
            Membership::PKEY_AGENCY_HE,
//            Membership::PKEY_AGENCY_HP,
//            Membership::PKEY_AGENCY_HJ_A,
            Membership::PKEY_HOMOEOPATH,
            Membership::PKEY_JPHMA_ANIMAL,
            Membership::PKEY_JPHMA_IC,
            Membership::PKEY_HAS_QX_SCIO,
        ];

        // 詳細クリック時に表示対象とするMembership
        $detail_disp_memberships = [
            Membership::PKEY_HOMOEOPATH,
            Membership::PKEY_JPHMA_IC,
            Membership::PKEY_STUDENT_INTEGRATE,
            Membership::PKEY_STUDENT_TECH_COMMUTE,
            Membership::PKEY_STUDENT_FH,
            Membership::PKEY_STUDENT_IC,
            Membership::PKEY_AGENCY_HE,
//            Membership::PKEY_AGENCY_HJ_A,
//            Membership::PKEY_AGENCY_HP,
            Membership::PKEY_JPHMA_TECHNICAL,
            Membership::PKEY_STUDENT_TECH_ELECTRIC,
            Membership::PKEY_CENTER_HOMOEOPATH,
            Membership::PKEY_JPHF_FARMER,
            Membership::PKEY_JPHMA_ANIMAL,
            Membership::PKEY_JPHMA_ZEN,
            Membership::PKEY_HAS_QX_SCIO,
        ];

        // TODO: ActiveQueryを２回呼び出して対応している。ArrayDataproviderでGridViewを作成できるように改修し、QueryをArray化して分割すればQuery発行は一度で済むはず。2017/02/03 m_kawai
        // 参考 http://stackoverflow.com/questions/27824977/using-yii2-gridview-with-array-of-data?rq=1
        
        // 並び替えたデータ8件から前半4件を取り出す
        // 参考：http://stackoverflow.com/questions/32155900/use-limit-range-in-yii2
        $place_query = Membership::find()
                        ->where(['in', 'membership_id', $membership_array])
                        //->orderBy([new \yii\db\Expression('FIELD (membership_id, ' . implode(',', array_reverse($membership_array)).') desc limit 4')]);
                        ->orderBy([new \yii\db\Expression('FIELD (membership_id, ' . implode(',', array_reverse($membership_array)).') desc limit 2')]);
        
        // 並び替えたデータ8件から後半4件を取り出す
        // 参考：http://stackoverflow.com/questions/32155900/use-limit-range-in-yii2
        $member_query = Membership::find()
                        ->where(['in', 'membership_id', $membership_array])
                        ->orderBy([new \yii\db\Expression('FIELD (membership_id, ' . implode(',', array_reverse($membership_array)).') desc ')])
                        //->offset(4)
                        ->offset(2)
                        ->limit(5);
                
        ?>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $place_query,
            'pagination' => false,
        ]),
        'tableOptions' => ['class'=>'table'],
        'layout' => '{items}',
        'columns' => [
            [
                'class'           => \yii\grid\CheckboxColumn::className(),
                'name'            => 'membership_id',
                'multiple'        => false,
                'checkboxOptions' => function($model, $key, $index, $column)use($membership_id)
                {
                    return [
                        'value'   => $key,
                        'checked' => ! empty($membership_id) && in_array($key, $membership_id),
                        'id'      => "ipt{$key}",
                    ];
                }
            ],
            [
                'header'    => '施設で探す',
                'attribute' => 'name',
                'format'    => 'raw',
                'value'     => function($data, $key, $index, $column)
                {
                
                // TODO: 〜認定をカットして表示させる。データそのものが修正されれば必要なくなる処理。2017/02/03 m_kawai
                    $name = $data->name;
                    if(stristr($name, "認定")) {
                        $name = explode("認定",$data->name)[1];
                    }
                
//                    return Html::tag('label',$data->name,['for'=>"ipt{$key}"]);
                    return Html::tag('label',$name,['for'=>"ipt{$key}"]);
                },
            ],
        ],
        'rowOptions' => function($data, $key, $index, $grid)use($membership_id){
            if(in_array($data->membership_id, $membership_id))
                return ['class'=>'alert-info'];
        },
    ]) ?>
        
            <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $member_query,
            'pagination' => false,
        ]),
        'tableOptions' => ['class'=>'table'],
        'layout' => '{items}',
        'columns' => [
            [
                'class'           => \yii\grid\CheckboxColumn::className(),
                'name'            => 'membership_id',
                'multiple'        => false,
                'checkboxOptions' => function($model, $key, $index, $column)use($membership_id)
                {
                    return [
                        'value'   => $key,
                        'checked' => ! empty($membership_id) && in_array($key, $membership_id),
                        'id'      => "ipt{$key}",
                    ];
                }
            ],
            [
                'header'    => '資格から探す',
                'attribute' => 'name',
                'format'    => 'raw',
                'value'     => function($data, $key, $index, $column)
                {
                
                // TODO: 〜認定をカットして表示させる。データそのものが修正されれば必要なくなる処理。2017/02/03 m_kawai
                    $name = $data->name;
                    if(stristr($name, "認定")) {
                        $name = explode("認定",$data->name)[1];
                    }               
                    if(stristr($name, "QX-SCIOを使用しています")) {
                        $name = explode("しています",$data->name)[0];
                    }
 
//                    return Html::tag('label',$data->name,['for'=>"ipt{$key}"]);
                    return Html::tag('label',$name,['for'=>"ipt{$key}"]);
                },
            ],
        ],
        // チェックした行は、検索処理実行後、結果表示時にclass="alert-info"が設定され色が付く
        'rowOptions' => function($data, $key, $index, $grid)use($membership_id){
            if(in_array($data->membership_id, $membership_id))
                return ['class'=>'alert-info'];
        },
    ]) ?>
        
                <?= Html::submitButton('チェックした条件で検索',['class'=>'btn btn-block btn-default']) ?>
    </div>
</div>

<hr id="search_result">

<?php

$q = clone($query);

$prefs = $q->select('pref_id')->distinct()->orderBy(['pref_id'=>SORT_ASC])->column();
?>

<?php
foreach($prefs as $pref_id):
?>

<?php
    $q = clone($query);
    $q->andWhere(['pref_id' => $pref_id]);
?>
    <p></p>

    <h2><span>
        <?= ($pref = Pref::findOne($pref_id)) ? $pref->name : "その他" ?>
    </span></h2>

 
    
    <div class="form-group">
        
        <?php

             $dataProvider = new \yii\data\ActiveDataProvider([
                'query'      => $q->orderBy(Facility::tableName().'.facility_id ASC'),
                'pagination' => false,
             ]);
        ?>
        <?= //\yii\widgets\ListView::widget([
         \yii\grid\GridView::widget([
                'layout' => (($pref = Pref::findOne($pref_id)) ? $pref->name : "その他") ."に<b>".$dataProvider->getTotalCount()."件</b>あります\n{items}\n{pager}",
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                    'attribute' => '名前', 
                    'contentOptions' => ['class' => 'word-wrap', 'style'=>'min-width: 50px; max-width: 120px;'],
        //                'value' => $result["name"]
                    'value' =>function ($data) {
                        return $data->name;
                    },
                    ],
                    [
                    "attribute" => "addr",
                    'contentOptions' => ['class' => 'word-wrap', 'style'=>'max-width: 200px;'],
                    'value' => function ($data) {
                        return $data->addr;
                    },
                    ],
                    [
                    "attribute" => "電話番号",
                    'contentOptions' => ['class' => 'word-wrap', 'style'=>'min-width: 50px; max-width: 70px;'],
                    'value' => function ($data) {
                        return $data->tel;
                    },
                    ],
                   ['class' => 'yii\grid\DataColumn' ,
                        "attribute" => "詳細",
                        'format' => 'raw',
                    'contentOptions' => ['style'=>'width:40px;'],
                    'value' => function ($data) use( $detail_disp_memberships) {
                        $filtered=array();
/*
                        $memberships = Membership::find()->join('JOIN', CustomerMembership::tableName(),Membership::tableName().'.membership_id = '.CustomerMembership::tableName().'.membership_id')->where(['customer_id'=>$data->customer_id])->all();
                        #$memberships = yii\helpers\ArrayHelper::getColumn($memberships, 'name');
                        foreach($memberships as $member){
                            $name = $member['name'];
                            $id = $member['membership_id'];
                            if(!in_array($id, $detail_disp_memberships))
                                continue;
                            // TODO: 〜認定をカットして表示させる。データそのものが修正されれば必要なくなる処理。2017/02/03 m_kawai
                            if(stristr($name, "認定")) {
                                $name = explode("認定",$name)[1];
                            }
                            if(stristr($name, "QX-SCIOを使用しています")) {
                                $name = explode("しています",$data->name)[0];
                            }
                            
                            $filtered[] = $name;

                        }
*/

		        isset($data->summary) ? $summary = str_replace("\r\n","<br>",$data->summary) : $summary = "(未登録）<br />";
                                    return Html::a('詳細', "#",['class' => 'btn btn-success btn-sm', 'data-toggle' => 'modal', 'data-target' => '#detailModal', 'data-backdrop' => 'static', 'id' => 'modal-open-'.$data->customer_id,'onclick' => Html::decode(
                                    '
                                        var modal = $("#detailModal");
                                        var dialog = modal.find(".modal-dialog");
                                        modal.css("display", "block");
                                        dialog.css("margin-top", Math.max(0, ($(window).height() - dialog.height()) / 2));
                                    $("#modalName").text("'.$data->name.'");
                                    $("#modalMembership").html("<strong style=\"color:#777;\">'.implode("<br />", $filtered).'</strong>");
                                    $("#modalAddr").html("住所：〒'.$data->zip01.'-'.$data->zip02.' '.$data->addr.'");
                                    $("#modalSummary").html("紹介文：'.$summary.'");
                                    $("#modalTel").html("電話：'.(isset($data->tel) && strlen($data->tel) > 0 ? $data->tel : '（未登録）<br />').'");
                                    $("#modalFax").html("FAX：'.(isset($data->fax) && strlen($data->fax) > 0 ? $data->fax : '（未登録）<br />').'");
                                    $("#modalEmail").html("Email：'.(isset($data->email) && strlen($data->email) > 0 ? '<a href=\"mailto:'.$data->email.'\">'.$data->email.'</a>' : '（未登録）<br />').'");
                                    $("#modalUrl").html("URL：'.(isset($data->url) && strlen($data->url) > 0 ? '<a href=\"'.$data->url.'\" target=\"_blank\">'.$data->url.'</a>' : '（未登録）<br />').'");

                                ')]);
                    }],
                    ['class' => 'yii\grid\DataColumn' ,
                      "attribute" => "リンク",
                        'format' => 'raw',
                    'contentOptions' => ['style'=>'width: 40px;'],
                    'value' => function ($data) {
                                    return isset($data->url) ? Html::a('Link', $data->url,['class' => 'btn btn-info btn-sm','target'=>'_blank']) : "";
                        },
                    ],
                ]
            ]) ?>

    </div>

<?php endforeach ?>

<p><p>
<div class="row">
<?php if(0 == $query->count()): ?>

    <?php if($pref_id): ?>
        <h2><span>
            <?= ($pref = Pref::findOne($pref_id)) ? $pref->name : "その他" ?>
        </span></h2>
    <?php endif ?>

    <?php if($pref_id || $model->getDirtyAttributes()): ?>
        <p class="alert alert-warning">
            見つかりません
        </p>
    <?php endif ?>

<?php endif ?>
</div>

<?php $form->end() ?>

           <?php \yii\bootstrap\Modal::begin([
        'id' => 'detailModal',
        'class' => 'modal fade bg-gray',
        'header' => Html::tag('h3', "",['id' => 'modalName', 'style' => 'color:#003F74']),
        'options' => ['style'=>'position:fixed;
            z-index: 10000;
            width: 90%;
            right:0; 
            left: 50%;
            transform: translateX(-50%);
            overflow:auto;'],
        'clientOptions' => [
            'backdrop' => 'static',
            'keyboard' => true
        ],
    ]);
       echo "<div class='modal-detail' style='font-size:116%;'>";
       echo "<div class='modal-detail-member' id='modalMembership' style='text-align: left; float:left; padding-right:30px; padding-bottom:15px;'></div>"; 
       echo "<div class='modal-detail-addr' style='float:left; padding-right:30px;'>";
       echo "<div id='modalAddr'></div>"; 
       echo "<div id='modalTel'></div>"; 
       echo "<div id='modalFax'></div>"; 
       echo "</div>";
       echo "<div class='modal-detail-link' style='float:left;'>";
       echo "<div id='modalEmail'></div>"; 
       echo "<div id='modalUrl'></div>"; 
       echo "</div>";
       echo "</div>";
       echo "<div id='modalSummary' style='font-size: 116%; padding-top:15px; clear:left;'></div>"; 
       echo '        <div class="modal-footer" style="clear:left;">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>';

     \yii\bootstrap\Modal::end();?>
