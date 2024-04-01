<?php

namespace frontend\modules\sodan;
use Yii;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/sodan/Module.php $
 * $Id: Module.php 1637 2015-10-11 11:12:30Z mori $
 */

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'frontend\modules\sodan\controllers';
    public $defaultRoute        = 'karute/index';
    public $homoeopathid;

    public function init()
    {
        parent::init();

        $this->setHomoeopath();
    }

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        return true;
    }

    public static function getName()
    {
        return "健康相談";
    }

    public function setHomoeopath()
    {
        if(Yii::$app->user->isGuest)
            return;

        $user = Yii::$app->user->identity;
        $name = $user->name01 . $user->name02;

        $model = new \common\models\webdb20\KaruteHomoeopath();
        $name = preg_replace('/^HE/u', '', $name);
        if('euc-jp' == $model->db->charset)
            $name = mb_convert_encoding($name, 'CP51932', 'UTF-8');// convert back to EUC-WIN-JP

        $homoeopath = $model->find()->where(['like','syoho_homeopath',$name])->one();
        if(! $homoeopath)
            throw new \yii\base\Exception(sprintf('あなた(%s)のhomoeopathidが見つかりません', $user->name));

        $this->homoeopathid = $homoeopath->syoho_homeopathid;
    }
}
