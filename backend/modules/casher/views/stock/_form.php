<?php
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/stock/_form.php $
 * $Id: _qty.php 2293 2016-03-24 03:40:01Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Inventory
 */

$jscode = "
var p_data = {};
$('#ac1').on('click', function(){
    $('#ac1').autocomplete({
        source: function(req, resp){ // respはコールバック関数
            $.ajax({
                url: 'search',
                type: 'POST',
                cache: false,
                dataType: 'json',
                data: {
                  name: req.term // ユーザーの入力値
                },
                success: function(o){
                    p_data = o; // 変数に格納
                    resp(o);
                },
                error: function(xhr, ts, err){
                    resp('');
                }
              });
        }
    })
});

$('#ac1').on('change', function(){
    var contact = JSON.parse(p_data);
});


";
$this->registerJs ( $jscode );

$qtyOption = [];
$i = $model->maximum_qty;

while ( $i >= 0 ) {
    $qtyOption [$i] = $i;
    $i --;
}

?>
<div id="btn-<?= $model->stock_id ?>" class="">

<?php
    $form = \yii\bootstrap\ActiveForm::begin([
        'action' => Url::to(['create',
                             'page'  => Yii::$app->request->get('page'),
        ]),
        'method' => 'post',
    ]);
?>

    <div class="col-md-6">
        <?= $form->field($model, 'name')->textInput(['name' => 'name', 'id'=>'ac1']); ?>
        <!-- 補完候補を表示するエリア -->
        <div id="suggest" style="display:none;"></div>

        <?php
            \yii\jui\AutoComplete::widget([
                // 'attribute'     => 'name',
                'name'          => '商品名',
                'value'         => $model->ean13 ? $model->product->name : '',
                'options'       => [
                    'class' => 'form-contorl has-success',
                ],
            ]);

            // echo $form->field($model, 'product_id')->hiddenInput(['name'=>'product_id']);
            // echo $form->field($model, 'ean13')->hiddenInput(['name'=>'ean13']);

            echo $form->field($model, 'actual_qty')->textInput();
            echo $form->field($model, 'threshold')->textInput();
            echo $form->field($model, 'branch_id')->dropdownList($branchs, ['id' => 'stock-branch_id' ]);

            echo Html::submitButton('登録', [
                'class' => 'pull-left btn btn-success',
                'title' => '在庫管理商品を登録する',
            ]);

            $form->end();
        ?>
    </div>
</div>
