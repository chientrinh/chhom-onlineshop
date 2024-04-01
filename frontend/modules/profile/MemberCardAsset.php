<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/profile/MemberCardAsset.php $
 * $Id: MemberCardAsset.php 1660 2015-10-14 08:48:36Z mori $
 *
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\modules\profile;

/**
 * @author Reiko Mori <mori@homoeopathy.co.jp>
 * @since r1659
 */
class MemberCardAsset extends \yii\web\AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
