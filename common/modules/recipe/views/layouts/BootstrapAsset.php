<?php
namespace common\modules\recipe\views\layouts;

/**
 * $URL: https://localhost:44344/svn/MALL/frontend/views/layouts/bootstrap.php $
 * $Id: bootstrap.php 1725 2015-10-29 09:53:17Z mori $
 *
 * @author Reiko Mori <mori@homoeopathy.co.jp>
 *
 * @var $this \yii\web\View
 * @var $content string
 */

class BootstrapAsset extends \yii\web\AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '/';
    public $css = [
        'css/site.css',
    ];

    public $js = [
        'js/lang-support-ja.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
