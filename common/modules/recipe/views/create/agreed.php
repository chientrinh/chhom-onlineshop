<?php
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/recipe/views/create/agreed.php $
 * $Id: index.php 3368 2017-06-01 13:09:13Z kawai $
 *
 * @var $this \yii\web\View
 */

$title = "会員登録";
$this->params['body_id'] = 'Signup';
$this->title = implode(' | ',array_merge(array_reverse($this->params['breadcrumbs']),[Yii::$app->name]));

$jscode = "
    $('#register').attr('disabled', 'disabled');
    // $('#img-banner').hide();

    isCheck($('.agreed'));

    $('.agreed').on('click', function() {
        isCheck($(this));
    });

    function isCheck(obj){
        if (obj.prop('checked') == false) {
            $('#register').attr('disabled', 'disabled');
        } else {
            $('#register').removeAttr('disabled');
        }
    }

    $('#guide-toggle').click(function(){
        $('#migration-guidance').toggle();
    });
";
$this->registerJs($jscode);
?>

<div class="signup-index">
    <?= $this->render('_tab') ?>
    <h1 class="mainTitle"><?= Html::encode($title) ?></h1>
    <p class="mainLead">当Webサイトの会員にご登録される方は、<br>
        <strong><?= Html::a("利用規約", ['/site/usage'], ['target' => '_blank']) ?></strong> をご確認・同意の上お進みください。<br>
        <br>
    <label>
        <input type="checkbox" class="agreed " name="agreed" value="1">
        &nbsp;利用規約に同意する
    </label><br><br>
    <?= Html::a("登録する", ['create-customer','agreed' => 1], ['class' => 'btn btn-success input-sm', 'id' => 'register']) ?>
    </p>
</div><!-- signup-index -->

