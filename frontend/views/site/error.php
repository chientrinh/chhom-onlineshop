<?php

use yii\helpers\Html;

/**
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/error.php $
 * $Id: error.php 1071 2015-06-05 06:10:38Z mori $
 *
 * @var $this yii\web\View
 * @var $name string
 * @var $message string
 * @var $exception Exception
 */

$this->title = $name . ' | ' . Yii::$app->name;
$this->params['body_id'] = 'Error';

?>
<div class="site-error">

    <h1><?= Html::encode($name) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>

	<p> The above error occurred while the Web server was processing your request. </p>
	<p> Please contact us if you think this is a server error. Thank you. </p>

</div>
