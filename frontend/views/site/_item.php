<?php
use yii\helpers\Html;

use \common\models\Company;

/**
 * ProductController implements the CRUD actions for Product model.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/_item.php $
 * $Id: _item.php 981 2015-05-02 08:58:09Z mori $
 */

$logo = null;
if(Company::PKEY_TY == $model->company_id)
{
    $logo = 'ty.png';
}
elseif(Company::PKEY_HJ == $model->company_id)
{
    $logo = 'hj.jpg';
}
elseif(Company::PKEY_HE == $model->company_id)
{
    $logo = 'he.jpg';
}
elseif(Company::PKEY_HP == $model->company_id)
{
    $logo = 'hp.jpg';
}
?>


<div class="col-lg-4">
<h3><?= Html::img(\yii\helpers\Url::to(sprintf('@web/img/logo/%s',$logo)),['width'=>'300px']) ?></h3>
<h2><?= $model->name ?></h2>
<p>
あいうえお あいうえお あいうえお あいうえお あいうえお あいうえお あいうえお あいうえお あいうえお あいうえお あいうえお あいうえお あいうえお あいうえお 
</p>
<?= Html::a("もっと見る",
            \yii\helpers\Url::toRoute([
                'company/view',
                'id'=>$model->company_id], ['class'=>"btn btn-default"])) ?>
</div>
