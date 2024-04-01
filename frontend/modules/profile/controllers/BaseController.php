<?php

namespace frontend\modules\profile\controllers;
use Yii;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/profile/controllers/BaseController.php $
 * $Id: BaseController.php 4248 2020-04-24 16:29:45Z mori $
 */

abstract class BaseController extends \yii\web\Controller
{
    public $customer;

    public $nav;

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
                        'actions'=> ['index','view','member-card', 'rank-up'],
                        'allow'  => true,
                        'roles'  => ['@'], // allow authenticated users
                        'verbs'  => ['GET'],
                    ],
                    [
                        'actions'=> ['create','update'],
                        'allow'  => true,
                        'roles'  => ['@'], // allow authenticated users
                        'verbs'  => ['GET','POST'],
                    ],
                    [
                        'actions'=> ['delete'],
                        'allow'  => true,
                        'roles'  => ['@'], // allow authenticated users
                        'verbs'  => ['GET','POST'],
                    ],
                    [
                        'allow'  => false, // everything else is denied
                    ],
                ],
            ],
        ];
    }

    /**
     * set Customer model
     * @return void
     */
    public function init()
    {
        parent::init();

        if(Yii::$app->user->isGuest)
            return;

        $customer = \common\models\Customer::findOne(Yii::$app->user->id);
        if(! $customer)
        {
            Yii::error(sprintf("internal error: Customer not found by app->user->id (%d)", Yii::$app->user->id));
            throw new \yii\web\NotFoundHttpException();
        }

        $this->customer = $customer;
    }
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $items = [
                ['label' => "トップ",     'url' => ['/profile/default/index'], ],
                ['label' => "お気に入り",    'url' => ['/profile/favorite/index'], ],
                ['label' => "ご購入の履歴",  'url' => ['/profile/history/index'], ],
                ['label' => "請求書の履歴",  'url' => ['/profile/invoice/index'], ],
                ['label' => "会員情報の確認・変更", 'url' => ['/profile/default/view'], ],
                ['label' => "請求先情報", 'url' => ['/profile/office/index'], ],
                ['label' => "提携施設", 'url' => ['/profile/facility/index'], ],
                ['label' => "口座振替", 'url' => ['/profile/debit/index'], ],
                ['label' => "住所録（ご自宅以外）", 'url' => ['/profile/addrbook/index'], ],
                ['label' => "購入済相談会チケット確認", 'url' => ['/profile/ticket/index'], ],
            ];

        if('echom-frontend' == Yii::$app->id) {
            $items = [
                ['label' => "トップ",     'url' => ['/profile/default/index'], ],
                ['label' => "会員情報の確認・変更", 'url' => ['/profile/default/view'], ],
                ['label' => "口座振替", 'url' => ['/profile/debit/index'], ],
            ];
        }
    

        $q = \common\models\Invoice::find()->active()->andWhere(['customer_id' => Yii::$app->user->id]);

        // 請求書が発行されていれば公開する 
      if(! $q->exists()) 
            unset($items[3]); // 請求書

        // 代理店のみ請求先情報を表示する
       if(! $this->customer->isAgency())
            unset($items[5]); // 請求先情報

        // 有資格者のみ提携施設を表示する
        // 提携施設リンクは１月１０日中に公開予定　2020/01/06 kawai
        if(! $this->customer->isFacility())
            unset($items[6]); // 提携施設

        // 開業前なので、本番環境ではYSD口振を非公開とする 2016.11.25 mori
        // 口座振替を非公開とする 
        // 口座振替を公開とする 
//        if('production' == YII_ENV)
//            unset($items[7]); // 口座振替

        $ticket = \common\models\DiscountProductLog::find()->andWhere(['customer_id' => $this->customer->customer_id])->all();
        if (!$ticket) {
            unset($items[9]);
        }

        $this->nav = \frontend\widgets\Nav::begin([
            'items'   => $items,
            'encodeLabels' => false,
        ]);

        return true;
    }

    /**
     * Render index page
     * @return html as string
     */
    abstract public function actionIndex();

    /**
     * Render view page
     * @return html as string
     */
    abstract public function actionView($id);

    /**
     * Render create page
     * @return html as string
     */
    abstract public function actionCreate();

    /**
     * Render update page
     * @return html as string
     */
    abstract public function actionUpdate($id);

    /**
     * Execute delete()
     * @return HTTP REDIRECT
     */
    abstract public function actionDelete();

    /* @return void */
    protected static function addFlash($key, $value)
    {
        Yii::$app->session->addFlash($key, $value);
    }

    public function render($view, $params)
    {
        $html = parent::renderPartial($view, $params);

        $labels = \yii\helpers\ArrayHelper::getColumn($this->view->params['breadcrumbs'],'label');
        krsort($labels);
        $this->view->title = implode(' | ', $labels) . ' | ' . Yii::$app->name;

        $this->view->params['body_id'] = 'Mypage';

        return parent::renderContent($html);
    }

}
