<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/config/bootstrap.php $
 * $Id: bootstrap.php 804 2015-03-19 07:31:58Z mori $
 */
Yii::setAlias('common', dirname(__DIR__));
Yii::setAlias('frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('console', dirname(dirname(__DIR__)) . '/console');
