<?php

namespace common\assets;

/**
 * @link $URL: https://tarax.toyouke.com/svn/MALL/common/assets/BootstrapAsset.php $
 * @version $Id: BootstrapAsset.php 1674 2015-10-16 20:19:06Z mori $
 */

class BootstrapAsset extends \yii\web\AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl  = '@web';

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
