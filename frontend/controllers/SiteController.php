<?php
namespace frontend\controllers;

use Yii;
use frontend\models\LoginForm;
use frontend\models\ContactForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use \yii\helpers\ArrayHelper;
use yii\filters\Cors;

/**
 * Site controller
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/controllers/SiteController.php $
 * $Id: SiteController.php 4248 2020-04-24 16:29:45Z mori $
 */
class SiteController extends \yii\web\Controller
{
    public $title;
    public $defaultAction = 'home';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->title = Yii::$app->name;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        // all pages are under construction except 'site/index'
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'actions'=> ['home','about','guide','faq','tax','howtologin','howtologin_sp','howtochkprf','howtochkprf_sp','howtoselect','howtoselect_sp',
                                     'howtoremedy','howtoremedyabc','howtokit','howtobuy','howtochange_qty','howtoaddad','howtoby_tekiyosyo','howtomk_tekiyosyo',
                                     'howtomk_original','howtodebit','howtoorder_he','howtoorder_hj','howtoorder_hp','howtosell_he','howtosell_hj','howtosell_hp','usage','legal','policy'],
                        'allow'  => true,
                        'verbs'  => ['GET'],
                    ],
                         [
                        'actions'=> ['guide'],
                        'allow'  => true,
                        'verbs'  => ['GET'],
                    ],
                    [
                        'actions'=> ['login','logintest','contact','contacta','error','captcha'],
                        'allow'  => true,
                        'verbs'  => ['GET','POST'],
                    ],
                    [
                        'actions'=> ['logout'],
                        'allow'  => true,
                        'roles'  => ['@'], // allow authenticated users
                        'verbs'  => ['POST'],
                    ],
                    [
                        'actions'=> ['forgot-password','renew-password'],
                        'allow'  => true,
                        'roles'  => ['?'], // allow guest users only
                        'verbs'  => ['GET','POST'],
                    ],
                    [
                        'allow'  => false, // everything else is denied
                    ],
                ],
            ],
            [
                'class' => Cors::className(),
                'cors' => [
                    'Origin' => ['https://mall.toyouke.com', 'https://ec.homoeopathy.ac','http://mall.toyouke.com', 'http://ec.homoeopathy.ac'],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'HEAD', 'OPTIONS'],
                    'Access-Control-Allow-Credentials' => true,
                    
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        // ... ここで何らかの条件に従って `$this->enableCsrfValidation` を設定する ...
        // 親のメソッドを呼ぶ。プロパティが true であれば、その中で CSRF がチェックされる。
        // var_dump($action);exit;
        if($action->id == 'mall-login' || $action->id == 'login') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionHome()
    {
        if('under_const' == YII_ENV) // don't show `index` until March 2016
        {
            if(Yii::$app->user->identity)
                return $this->redirect(['/profile']);
            else
                return $this->redirect(['/signup']);
        }

        if('echom-frontend' == Yii::$app->id) {
            $categories = \common\models\Category::find()->where(['like', 'name', 'ライブ配信'])->all();
            
            $model   = new \frontend\models\SearchProductMaster([
                'customer' => Yii::$app->user->identity,
                'category_id' => ArrayHelper::getColumn($categories, 'category_id')
            ]);
            
            $provider = $model->search();
            $provider->query->andWhere(['or', ['vial_id' => null],
                                            ['<>','vial_id',\common\models\RemedyVial::DROP],
            ]);
        
            // 画面でH1タグに出力する名前。URLで入力した文字列では「雑」などとなるためカテゴリーから検索しているわけだが・・・
            // カテゴリ名の指定が「雑貨・衣類」のように「・」でつながっていない場合、かつ検索結果のカテゴリリストに含まれていない場合、検索結果の先頭を取る
             $name = '<div style="font-size: 50%">
         <a href="https://ec.homoeopathy.ac/program/tv_program.html">番組表はここをクリック</a><br>
         <a href="https://www.homoeopathy.ac">CHhom公式サイト</a><br>
         <a href="https://ec.homoeopathy.ac/guide">初めての方へ</a><br>
         <a href="https://www.homoeopathy.ac/news/27504/" target=new>オンライン相談アプリ『ホメチューブ』が始まりました！簡易相談！今すぐここをクリック！</a></div>';

if(file_exists("/dev/shm/insta_live_on_stream_key.php")){
             $name = '<div style="border:2px solid #999; margin-top:5px; padding:5px; background-color: yellow;">本日１９時からとようけＴＶでみなのホメオパシーLIVEを行います！<br>１９時になりましたら、とようけＴＶへＧＯ！<br>１９時に<b><a href="https://tv.toyouke.com" target="_blank"><span style="color:red;"><storong>ここをクリック！</strong></span></a></b></div>';
}

//             $name = '<div style="border:2px solid #999; margin-top:5px; padding:5px; background-color: yellow;">本日13:30～14:00ぐらいからとようけＴＶ－ｗｅｂでインスタライブ第二部を行います！<br>13:30～14:00ぐらいになりましたら、とようけＴＶ－ｗｅｂへＧＯ！<br>13:30～14:00ぐらいに<b><a href="https://tv.toyouke.com" target="_blank"><span style="color:red;"><storong>ここをクリック！</strong></span></a></b></div>';
//             $name = '<div style="border:2px solid #999; margin-top:5px; padding:5px; background-color: yellow;">とようけＴＶ－ｗｅｂでインスタライブ第二部を行います！<br>とようけＴＶ－ｗｅｂへＧＯ！<br><b><a href="https://tv.toyouke.com" target="_blank"><span style="color:red;"><storong>ここをクリック！</strong></span></a></b></div>';
if(file_exists("/dev/shm/minogashi.dat")){
             $name = '<div style="border:2px solid #999; margin-top:5px; padding:5px; background-color: yellow;">本日１９時からとようけＴＶでみなのホメオパシーＴＶライブが行われました！<br>見逃した方はとようけＴＶへＧＯ！<br>見逃し配信<b><a href="https://tv.toyouke.com/video/397" target="_blank"><span style="color:red;"><storong>ここをクリック！</strong></span></a></b></div>';
}
if(file_exists("/dev/shm/minogashi2.dat")){
             $name = '<div style="border:2px solid #999; margin-top:5px; padding:5px; background-color: yellow;">12/1（木）10:00～<br>【大阪ライブ】「基本のレメディーを使いこなす3回講座」第2回開催中！！<br>【見逃しアーカイブ配信】 2022年12月6日（火）26:00まで<br>お申し込みは<b><a href="https://ec.homoeopathy.ac/product/3602" target="_blank"><span style="color:red;"><storong>ここをクリック！</strong></span></a></b></div>';
}
            
            return  $this->render('../category/echom/view', [
                'title'       => Yii::$app->name,
                'h1'          => $name,
                'searchModel' => $model,
                'dataProvider'=> $provider,
                'categories'  => $categories,
            ]);
            
            
            // return $this->render('echom/index');
        }

        // 開業前は非公開とする
        return $this->render('index');
    }
    public function actionGuidance()
    {
        return $this->render('guidance');
    }
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionGuide()
    {
        return $this->render('guide');
    }
    
    public function actionHowtologin()
    {
        return $this->render('howtologin');
    }
    
    public function actionHowtologin_sp()
    {
        return $this->render('howtologin_sp');
    }
    
    public function actionHowtochkprf()
    {
        return $this->render('howtochkprf');
    }

        public function actionHowtochkprf_sp()
    {
        return $this->render('howtochkprf_sp');
    }

    public function actionHowtoselect()
    {
        return $this->render('howtoselect');
    }
    
    public function actionHowtoselect_sp()
    {
        return $this->render('howtoselect_sp');
    }
    
    public function actionHowtoremedy()
    {
        return $this->render('howtoremedy');
    }
    
    public function actionHowtoremedyabc()
    {
        return $this->render('howtoremedyabc');
    }
    
    public function actionHowtokit()
    {
        return $this->render('howtokit');
    }
    
    public function actionHowtobuy()
    {
        return $this->render('howtobuy');
    }
    
    public function actionHowtochange_qty()
    {
        return $this->render('howtochange_qty');
    }
    
    public function actionHowtoaddad()
    {
        return $this->render('howtoaddad');
    }
    
    public function actionHowtoby_tekiyosyo()
    {
        return $this->render('howtoby_tekiyosyo');
    }
    
        public function actionHowtomk_tekiyosyo()
    {
        return $this->render('howtomk_tekiyosyo');
    }
    
        public function actionHowtomk_original()
    {
        return $this->render('howtomk_original');
    }
    
        public function actionHowtodebit()
    {
        return $this->render('howtodebit');
    }
    
        public function actionHowtoorder_he()
    {
        return $this->render('howtoorder_he');
    }

        public function actionHowtoorder_hj()
    {
        return $this->render('howtoorder_hj');
    }

    public function actionHowtoorder_hp()
    {
        return $this->render('howtoorder_hp');
    }
    
    public function actionHowtosell_he()
    {
        return $this->render('howtosell_he');
    }

    public function actionHowtosell_hj()
    {
        return $this->render('howtosell_hj');
    }
    
    public function actionHowtosell_hp()
    {
        return $this->render('howtosell_hp');
    }

    public function actionFaq()
    {
        return $this->render('faq');
    }
    
    public function actionTax()
    {
        return $this->render('tax');
    }


    public function actionLegal()
    {
        return $this->render('legal');
    }
    
    public function actionPolicy()
    {
        return $this->render('policy');
    }

    public function actionUsage()
    {
        return $this->render('usage');
    }

    public function actionLogin()
    {
        if (! \Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $prev_id = Yii::$app->session->id;
        $model   = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {

            // ここで代理店ユーザーならデフォルト支払いは代引きではない
            $customer = Yii::$app->user->identity;

                if((\common\models\CustomerGrade::PKEY_AA <= $customer->grade_id) && isset($customer->ysdAccount->detail))
                {
                    $params['payment_id'] = \common\models\Payment::PKEY_DIRECT_DEBIT;
                }
                elseif($customer->isAgency())
                {
                    $params['payment_id'] = \common\models\Payment::PKEY_BANK_TRANSFER;
                }

                if('echom-frontend' == Yii::$app->id) {
                    if((\common\models\CustomerGrade::PKEY_AA <= $customer->grade_id) && isset($customer->ysdAccount->detail))
                    {
                        $params['payment_id'] = \common\models\Payment::PKEY_DIRECT_DEBIT;
                    }
                    else
                    {
                        $params['payment_id'] = \common\models\Payment::PKEY_CREDIT_CARD;
                    } 
                }

                if(! \frontend\modules\cart\Module::updateSession($prev_id, isset($params) ? $params : null)) {
                     Yii::warning('updateSession() failed', $this->className().'::'.__FUNCTION__);
                }

            return $this->goBack();

        } else {
            $login = 'login';
	    if('echom-frontend' == Yii::$app->id) {
		$login = 'echom/login';
            }

            return $this->render($login, [
                'model' => $model,
            ]);
        }
    }

    public function actionLogintest()
    {
        if (! \Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $prev_id = Yii::$app->session->id;
        $model   = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {

            // ここで代理店ユーザーならデフォルト支払いは代引きではない
            $customer = Yii::$app->user->identity;

                if((\common\models\CustomerGrade::PKEY_AA <= $customer->grade_id) && isset($customer->ysdAccount->detail))
                {
                    $params['payment_id'] = \common\models\Payment::PKEY_DIRECT_DEBIT;
                }
                elseif($customer->isAgency())
                {
                    $params['payment_id'] = \common\models\Payment::PKEY_BANK_TRANSFER;
                }

                if('echom-frontend' == Yii::$app->id) {
                    if((\common\models\CustomerGrade::PKEY_AA <= $customer->grade_id) && isset($customer->ysdAccount->detail))
                    {
                        $params['payment_id'] = \common\models\Payment::PKEY_DIRECT_DEBIT;
                    }
                    else
                    {
                        $params['payment_id'] = \common\models\Payment::PKEY_CREDIT_CARD;
                    } 
                }

                if(! \frontend\modules\cart\Module::updateSession($prev_id, isset($params) ? $params : null)) {
                     Yii::warning('updateSession() failed', $this->className().'::'.__FUNCTION__);
                }

            return $this->goBack();

        } else {
            $login = 'login';
	    if('echom-frontend' == Yii::$app->id) {
		$login = 'echom/logintest';
            }

            return $this->render($login, [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        \frontend\modules\cart\Module::flushItemCount();

        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContacta()
    {
print"xxxx";exit;
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['supportEmail'])) {
                Yii::$app->session->addFlash('success', Yii::$app->name.'へお問い合わせいただきまして誠にありがとうございます。担当者より折り返しご返信差し上げますまでしばらくお待ちください。');
            } else {
                Yii::$app->session->addFlash('error', '自動配信にて不具合が発生したもようですが、お問合せ内容はシステムに記録されました。ご回答差し上げるまで、しばらくお待ちください。あるいは営業時間に電話／FAXにてお問い合わせくださいますようお願いします');
            }
            return $this->refresh();
        } else {
            $company = \common\models\Company::findOne(1);
            return $this->render('contact', [
                'model'   => $model,
                'company' => $company,
            ]);
        }
    }

    public function actionForgotPassword()
    {
        $model = new \frontend\models\ForgotPasswordForm();
        $ret   = false;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail())
            {
                Yii::$app->getSession()->addFlash('success',"メールが送信されました。");
                $ret = true;
            }
            else
                Yii::$app->getSession()->addFlash('error', sprintf("ご指定のメールアドレスへ送信できませんでした。お手数ですが、システム担当者へお問い合わせください。<br>連絡先：<a href='mailto:%s'>%s</a>",Yii::$app->params['adminEmail'],Yii::$app->params['adminEmail']));
        }

        if($ret)
            return $this->render('forgotPassword_thankyou', [
                'model'  => $model,
            ]);

        return $this->render('forgotPassword', [
            'model'  => $model,
        ]);
    }

    public function actionRenewPassword($token)
    {
        try {
            $model = new \frontend\models\RenewPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword())
        {
            Yii::$app->getSession()->addFlash('success', '新しいパスワードが保存されました');

            return $this->redirect(['site/login']);
        }

        return $this->render('renewPassword', [
            'model' => $model,
        ]);
    }

    public function actionView($page = 'index')
    {
        try
        {
            return $this->render($page);
        }
        catch (InvalidParamException $e)
        {
            throw new HttpException(404);
        }
    }
}
