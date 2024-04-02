<?php

namespace common\modules\member;

/**
* $URL: https://tarax.toyouke.com/svn/MALL/common/modules/member/Module.php $
* $Id: Module.php 3013 2016-10-23 03:17:48Z mori $
*/
use Yii;
use \yii\helpers\ArrayHelper;
use \common\models\Payment;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'common\modules\member\controllers';
    public $pointBack;

    public function init()
    {
        parent::init();

        if(! isset($this->pointBack) ||
           ! isset($this->pointBack[\common\models\Product::PKEY_TORANOKO_G_ADMISSION]) ||
           ! isset($this->pointBack[\common\models\Product::PKEY_TORANOKO_N_ADMISSION])
        )
            throw new \yii\base\InvalidConfigException('pointBack is not set');
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow'   => true,
                        'roles'   => ['worker'],
                    ],
                    [
                        'actions' => ['create'],
                        'allow'   => true,
                        'roles'   => ['?'],
                        'verbs'   => ['POST'],
                    ],
                    [
                        'allow'         => true,
                        'roles'         => ['@'],
                        'matchCallback' => function ($rule, $action)
                        {
                            $user = Yii::$app->user->identity;
                            return ($user instanceof \common\models\Customer &&
                                   ($user->isAgency() || $user->isHomoeopath())  );
                        },
                    ],
                    [
                        'allow' => false,
                        'roles' => ['?'], // deny guest
                        'denyCallback' => function ($rule, $action)
                        {
                            throw new \yii\web\NotFoundHttpException();
                        },
                    ],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        if('app-frontend' === Yii::$app->id)
            $this->layout = 'bootstrap';

        $this->initViewParams($action);

        return true;
    }

    private function initViewParams($action)
    {
        $route = ['default'=>'豊受モール会員','toranoko'=>'とらのこ会員','oasis'=>'とらのこ会報誌「オアシス」'];
        $label = ['index'=>'一覧','create'=>'入会','update'=>'更新','view'=>'表示'];
        $ctrl  = Yii::$app->controller;

        $crumbs = [
            ['label'=>'マイページ',         'url'=>['/profile']],
            ['label'=>$route[$ctrl->id],  'url'=>['index']   ],
            ['label'=>ArrayHelper::getValue($label,$action->id)],
        ];
        if('app-backend' == Yii::$app->id)
            array_shift($crumbs);

        foreach($crumbs as $crumb)
            if($crumb['label']) $ctrl->view->params['breadcrumbs'][] = $crumb;
        krsort($crumbs);

        $ctrl->view->title  = implode(' | ', ArrayHelper::getColumn($crumbs, 'label'));
        $ctrl->view->title .=         ' | ' . Yii::$app->name;
    }

    /* @retutn その状況で選択可能な支払い方法を返す */
    public function getPayments($backend)
    {
        if($backend)
            $param = [Payment::PKEY_CASH,Payment::PKEY_BANK_TRANSFER];
        else
            $param = [Payment::PKEY_CASH];

        return Payment::find()->where(['payment_id' => $param])->all();
    }

}
