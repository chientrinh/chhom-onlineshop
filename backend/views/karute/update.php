<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/karute/update.php $
 * $Id: update.php 1637 2015-10-11 11:12:30Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\webdb20\Karute
 */

?>
<div class="karute-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
