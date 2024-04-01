<?php

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/pointing/views/default/search.php $
 * $Id: search.php 1226 2015-08-02 04:57:39Z mori $
 */

use \yii\helpers\Html;

$this->params['body_id'] = 'Mypage';

?>

<div class="cart-view">

	<h2>
        <span>
        <?= $this->context->module->name ?>
            <small><?= Yii::$app->controller->company->name ?></small>
        </span>
    </h2>

  <div class="col-md-12">

      <div class="panel-heading">
          <?= Yii::$app->controller->nav->run() ?>
      </div>

      <div class="panel-heading">
          <?= Yii::$app->controller->nav2->run() ?>
      </div>

      <?= $this->render($viewFile, [
          'dataProvider' => $dataProvider,
          'searchModel'  => $searchModel,
      ]) ?>

  </div>

</div>
