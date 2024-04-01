<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/assets/AppAsset.php $
 * $Id: AppAsset.php 1150 2015-07-15 03:51:32Z mori $
 *
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author Reiko Mori <mori@homoeopathy.co.jp>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css?date=20240221',
    ];

    public $js = [
        'js/function.js',
        'js/lang-support-ja.js',
        'js/bxstarter.js', // required for footer @ frontend/views/layout/main.php
        'js/flexslider/jquery.bxslider.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
