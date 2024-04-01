<?php
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/signup/views/default/index.php $
 * $Id: index.php 3448 2017-06-27 02:30:30Z kawai $
 *
 * @var $this \yii\web\View
 */

$title = "会員登録";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'Signup';
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

    <h1 class="mainTitle"><?= Html::encode($title) ?></h1>

    <p class="mainLead">当Webサイトの会員にご登録される方は、<br>
        <strong><?= Html::a("利用規約",['/site/usage'],['target'=>'_blank']) ?></strong> をご確認・同意の上お進みください。<br>
        <br>
    <label>
        <input type="checkbox" class="agreed " name="agreed" value="1">
        &nbsp;利用規約に同意する
    </label><br><br>
    <?= Html::a("登録する", ['create','agreed'=>1], ['class'=>'btn btn-success input-sm', 'id'=>'register']) ?>
    </p>

    <div class="row">

<?php
    if("release" != YII_ENV) {
        
echo '
        <div class="alert alert-warning col-md-12">
        <p><strong>★豊受モールオープン延期のお知らせ</strong></p>
        <p>
            豊受オーガニクスショッピングモールは<br/>
            より充実したサービスをご提供するために、オープンは2016年秋に延期となりました。<br/>
            皆様には再三の延期となりましたこと、大変申し訳ありません。<br/>
            もうしばらくお待ちくださいますようお願いいたします。
        </p>
    </div>

    <div id="img-banner" class="text-center">
        <img src="/img/under_construction.jpg" alt="ただいま工事中です" style="max-width:400px">
    </div>';
    } 
?>
        
     <div class="sub-menu regist-menu" id="sub-menu">
	    <div class="new-regist col-md-12">
            <!-- <h2><span>初めてのご登録</span></h2> -->
<?php
    if("release" != YII_ENV) {
        
echo '          <!-- 2015.12.01 公開予定まで受付しない旨を表示する -->
            <p class="help-block">
                2016年秋、本運用開始（予定）より新規の会員登録を受け付けます
            </p>
            <p>';
?>
                <?= Html::a("登録する", ['create','agreed'=>1], ['class'=>'btn btn-default col-md-6','disabled'=>'disabled']) ?>
    <?php     echo'   </p>';
     } ?>
            <!-- <p>新しく豊受モール会員になるには、こちらからお進みください。</p> -->
            <!-- <p　class="col-md-12 col-lg-12">
            <?= Html::a("登録する", ['create','agreed'=>1], ['class'=>'btn btn-success col-md-6', 'id'=>'register', 'style'=>'text-align: center']) ?>
            </p> -->

	    </div>
<!--
	    <div class="change-regist col-md-8">
		    <h2><span>既存の会員・学生情報を移行する</span></h2>

		    <p>
                <?= Html::a("移行手続きのご案内",'#',['id'=>'guide-toggle']) ?>
            </p>

            <div id="migration-guidance" style="display:none">
		        <p>
                    他社の既存のご登録情報を移行することで、会員情報の入力の手間を省くことができます。お客様のお手元に豊受モール会員証がある、ないによって Aグループ、Bグループと分かれております。 
                    <ul style="list-style-type:square">
                        <li>
                            豊受モール会員証が現在、お手元にある: Aグループ 
                        </li>
                        <li>
                            豊受モール会員証が現在、お手元にない: Bグループ 
                        </li>
                    </ul>
                    詳しくは、下記の分類をご覧ください。
                </p>
                <p>
                    ※複数の会員・学生等に所属する方で、豊受モール会員証が届いた人は、Ａグループの「移行する」を選んでください。届いた豊受モール会員証の会員番号と台紙に記載の仮パスワードでログインしてください。 
                </p>

                <p>
                    ※豊受モール会員証が現在、お手元にない: Ｂグループの人で 
                </p>
                <ol>
                    <li>
                        日本豊受自然農のとようけ会会員
                    </li>
                    <li>
                        CHhomショップ会員
                    </li>
                </ol>
                <p>
                    を兼ねるお客様は、Bグループの「とようけ会から移行する」を選んでください。Ｂグループに該当するお客様は、それぞれの会で登録されているＩＤとパスワードでログインしてください。 
                </p>
                <p>
                    ※豊受モール開始までは、とらのこ会の「とらのこポイント」と、日本豊受自然農のとようけ会の「とようけポイント」の各ポイントサービスは継続します。豊受モール開始とともに、上記のポイントサービスは終了させていただき、その時点で、お客様が保有する各ポイントは、豊受モールの豊受ポイントとして、合算し、すべて移行されます。
                </p>
                <p>
                    例えば、AグループとBグループの会員を現在、兼ねているお客様は、Ａグループの登録画面で、豊受モール会員へ移行した瞬間に、自動的にシステムがお客様の電話番号 (携帯番号含む) を元に、Ｂグループの顧客情報との照合も行い、同一顧客と判明された場合、各社のポイント残が、豊受モール開始時点ですべて合算されて、お客様の豊受モールの豊受ポイントとなります。
                </p>
            </div>--><!-- migrate-guidance -->

<!--            <div class="panel panel-default">
                <div class="panel-heading">
                    Ａグループ
                </div>
                <div class="panel-body">
                    <h4>豊受モール会員証が届いた人</h4>	
                    <ul style="list-style-type:circle">
                        <li>
                            ホメオパシーとらのこ会会員
                        </li>
                        <li>
                            ファミリーホメオパス養成コース学生
                        </li>
                        <li>
                            インナーチャイルドセラピスト養成コース学生
                        </li>
                        <li>
                            CHhom４年制コース学生
                        </li>
                        <li>
                            認定ファミリーホメオパス
                        </li>
                        <li>
                            認定インナーチャイルドセラピスト
                        </li>
                        <li>
                            JPHMA認定ホメオパス
                        </li>
                        <li>
                            JPHMA専門会員
                        </li>
                    </ul>
		            <p class="text-center">
                        <?*= Html::a("移行する", ['he/search','agreed'=>1], ['class'=>'btn btn-success col-md-3']) ?>
                    </p>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    Ｂグループ
                </div>
                <div class="panel-body">
		            <h4>豊受モール会員証が届いていない人</h4>	
                    <ul style="list-style-type:circle">
                        <li>
                            <p>
                                日本豊受自然農のとようけ会会員
                            </p>
                            <p>
                                <?*= Html::a("とようけ会から移行する", ['ty/search','agreed'=>1], ['class'=>'btn btn-success']) ?>
                            </p>
                        </li>
                        <li>
                            <p>
                                CHhomショップ会員
                            </p>
                            <p>
                                <?*= Html::a("CHhomショップから移行する", ['he/search','agreed'=>1,'target'=>'ecorange'], ['class'=>'btn btn-success']) ?>
                            </p>
                        </li>
                        <?php //if(0): /* 自然の会のぼたんは表示しない、www.homoeopathy.co.jpから直接URLを貼ってもらう 2015.08.21 */ ?>
                            <li>
                                <p>
                                    ホメオパシージャパン(株)自然の会会員
                                </p>
                                <p>
                                    <?*= Html::a("自然の会から移行する", ['hj/search','agreed'=>1], ['class'=>'btn btn-success']) ?>
                                </p>
                            </li>
                        <?php //endif ?>
                        <li>
                            <p>
                                ホメオパシー出版通販サイト会員から移行する
                            </p>
                            <p>
                                <?*= Html::a("ホメオパシー出版から移行する", ['hp/search','agreed'=>1], ['class'=>'btn btn-success']) ?>
                            </p>
                        </li>
                    </ul>
                </div>--><!-- panel-body -->
<!--            </div>--><!-- panel -->
        </div><!-- change_regist -->
    </div><!-- sub-menu -->

    </div><!-- row -->

</div><!-- signup-index -->

