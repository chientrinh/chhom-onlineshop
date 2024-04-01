<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/profile/views/debit/index.php $
 * $Id: index.php 3761 2017-11-18 06:29:51Z naito $
 *
 * @var $this  \yii\web\View
 * @var $model \common\models\ysd\Account or null
 */
use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;
use \common\models\Purchase;
use \common\models\Payment;

$detail = ArrayHelper::getValue($model,'detail');
?>

<div class="cart-view">

  <h1 class="mainTitle">マイページ</h1>
  <p class="mainLead">このページでは出店企業・団体からのご優待や特典リンクをご案内します。</p>

  <div class="col-md-3">
	<div class="Mypage-Nav">
	  <div class="inner">
		<h3>Menu</h3>
          <?= Yii::$app->controller->nav->run() ?>
	  </div>
	</div>
  </div>

  <div class="col-md-9">
    <h2><span>口座振替</span></h2>

    <?php if(isset($detail)): /* 口座あり */?>
    <p>口座の登録は以下の日時をもって完了しました</p>
    <?= \yii\widgets\DetailView::widget([
        'model' => $detail,
        'options' => ['class'=>'table-condensed'],
        'template' => '<tr><th class="col-md-2">{label}</th><td>{value}</td></tr>',
        'attributes' => [
            'cdate',
            'created_at:date'
        ],
    ]) ?>

    <?php else: /* 口座なし */?>

    <p>
    ご登録いただくと、毎月まとめて後払いにてご注文いただけます。<br>
   （末日締め、翌26日にご指定の銀行口座より自動引き落とし）<br>
    <font color=#990066">※お申し込みできる銀行口座は、個人の口座に限られています。法人や団体の口座はお申し込みできませんので、ご了承願います。</font>
    </p>
    
        <?php if (isset($model) && (-1 == $model->expire_id)){ /* 手続き中 */ ?>
        <p class = "alert alert-info">
            現在、口座登録手続き中です。ご登録の確認ができるのは、ご登録されてから<strong>２〜５営業日後</strong>となっております。それまでは、代引でのご購入となります。<br>
            ご登録の確認が完了しますと、完了画面になりますので、それまでお待ちください。<font color="#990066">再度、ご登録の必要はございません。</font><br>
            ご登録の確認ができますと、カートのお支払い方法に口座振替が設定されます。代引に変更することも可能です。
        </p>
        <?php } ?>

    <?php endif ?>
    	
    <p class="text-left">
    	
    <?php
        $label = '';
        $class = 'btn';
        if(isset($detail)) /* 口座あり */
        {
            $label = '口座変更';
            $class .= ' btn-default';
        }
        else
        {
            $label = '登録';
            $class .= ' btn-warning';
        }
        if (isset($model) && (-1 == $model->expire_id)){ /* 手続き中 */
            $label = '再登録';
            $class = 'btn btn-default';
        }
        
        if(($rrq->hasErrors()) || (Yii::$app->session->hasflash('error'))) /* エラー */
        {
            $class .= ' disabled';
        }

        echo Html::a($label, ['create'], ['class'=>$class]);
    ?>
        
    <?php if(isset($detail)): /* 口座あり */ ?>
    <?php
        {
            $last_Ym = date("Y-m-01", strtotime ( '-1 month' , time() )) ;   
            $query = Purchase::find()->active()
                                     ->andWhere(['>=','create_date', '$last_Y_m' ])
                                     ->andWhere(['<','create_date', date('Y-m-1')])
                                     ->andWhere(['customer_id'=> Yii::$app->user->id])
                                     ->andWhere(['payment_id' => Payment::PKEY_DIRECT_DEBIT])
                                     ->andWhere(['paid' => 0]);
            $last_charge = (int) $query->select('SUM(total_charge)')->scalar();
            
            $query = Purchase::find()->active()
                                     ->andWhere(['>=','create_date', date('Y-m-1')])
                                     ->andWhere(['<=','create_date', date('Y-m-t')])
                                     ->andWhere(['customer_id'=> Yii::$app->user->id])
                                     ->andWhere(['payment_id' => Payment::PKEY_DIRECT_DEBIT])
                                     ->andWhere(['paid' => 0]);
            $this_charge = (int) $query->select('SUM(total_charge)')->scalar();

            $default = $model->find()->select('DEFAULT(credit_limit)')->scalar();
        }
    ?>
        <p class = "alert alert-info">
            <?php if(! $model->isValid() || ($model->credit_limit <= 0)): ?>
            ただいま口座振替はご利用いただけません。<br>
            <?php elseif($default != $model->credit_limit): ?>
            ただいまの振替限度額は1回あたり <?= number_format($model->credit_limit) ?>円です。<br>
            <?php endif ?>

            今月の振替金額は、<?= number_format($this_charge) ?>円の予定です。（来月２６日引き落とし予定） <br>
        </p>
        
    <?php else: /* 口座なし */?>

        <?php if (isset($model) && (-1 == $model->expire_id)){ /* 手続き中 */ ?>
        <p><font color="#990066">
            各金融機関での口座登録の手続きが完了していない場合は、再登録のボタンをクリックして、再度お手続きが可能です。<br>
            各金融機関での口座登録の手続きは、１日１回までとなっております。口座を変更される場合は、翌日以降に、改めて登録をお願いします。</font>
        </p>
        <?php } ?>

    <?php endif ?>        
    
    <p>    
    <?= Html::a('口座振替のご登録方法はこちらをご覧ください',['/site/howtodebit'],['target'=>'_blank'],['class'=>'pull-left']) ?>
    </p>



    </div>
  </div>

