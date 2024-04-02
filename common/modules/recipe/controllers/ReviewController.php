<?php
namespace common\modules\recipe\controllers;

use Yii;

/**
 * Prescriotion controller
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/recipe/controllers/ReviewController.php $
 * $Id: ReviewController.php 3460 2017-06-29 13:09:24Z kawai $
 */
class ReviewController extends \yii\web\Controller
{
    public $defaultAction = 'search';

    public $nav;

    public $crumbs = [
        'index'  =>['label'=>"一覧",'url'=>['/recipe/review/index']],
        'view'   =>['label'=>"表示",'url'=>['/recipe/review/view']],
        'search' =>['label'=>"検索",'url'=>['/recipe/review/search']],
    ];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    // allow everyone
                    [
                        'allow' => true,
                        'roles' => ['?','@'],
                    ],
                ],
            ],
        ];

    }

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->nav = \frontend\widgets\Nav::begin([
            'items'   => $this->crumbs,
        ]);

        $this->view->title = sprintf('%s | %s | %s',
                                     \yii\helpers\ArrayHelper::getValue($this->crumbs, $action->id.'.label', $action->id),
                                     $this->module->name,
                                     Yii::$app->name);

        $this->view->params['breadcrumbs'] = [
            ['label' => $this->module->name, 'url' => ['/recipe/review/index'],],
        ];

        if(isset($this->crumbs[$action->id]))
            $this->view->params['breadcrumbs'][] = $this->crumbs[$action->id];

        return true;
    }

    /**
     * display customer's own un-purchased recipes
     * with search form
     */
    public function actionIndex()
    {
        return $this->redirect(['search']);

        /* 2015.12.17 本機能(自分の適用書の一覧)を廃止：本部センターのメール相談後、代引きで届く適用書を受信拒否する人がいるため
        「もしそのようなことがあれば相談会の費用を支払わずにレメディーがお買い物できてしまう」のを防ぐ
        (信頼に基づく商行為に思い入れがあるプログラマ個人の思惑により以下の機能を復活させるかもしれない日に備えてコードを温存しておく)
        */
        if(Yii::$app->user->isGuest)
            return $this->redirect(['search']);

        $searchModel  = new \common\models\SearchRecipe([
            'client_id' => Yii::$app->user->id,
            'status'    => \common\models\Recipe::STATUS_INIT,
        ]);
        $dataProvider = $searchModel->search();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    /**
     * display search form
     */
    public function actionSearch()
    {
        $searchModel  = new \common\models\SearchRecipe([
//            'status'    => \common\models\Recipe::STATUS_INIT,
        ]);
        return $this->render('search',[
            'searchModel'  => $searchModel,
        ]);
    }

    /**
     * display searched recipe
     */
    public function actionView($id, $pw=null)
    {
        $model = $this->findModel($id, $pw);

        if(! $model) 
            return $this->redirect(['search']);

        return $this->render('view', [
            'model' => $model,
        ]);
    }
    
    /**
     * get recipe from own system
     */
    private static function findModel($id, $pw)
    {
        $model = null;

        if((null === $pw) && ! Yii::$app->user->isGuest)
            $model = \common\models\Recipe::find()->where([
                'recipe_id' => [abs($id), (0 - abs($id))],
                'client_id' => Yii::$app->user->id,
            ])->one();
        elseif(0 < strlen($pw))
            $model = \common\models\Recipe::find()->where([
                'recipe_id' => [abs($id), (0 - abs($id))],
                'pw'        => $pw,
            ])->one();

        // モール内部で見付からない場合、webdb18から検索する処理。不具合が多いのでコメントアウトする 2017/06/13 ticket:708
        // if(! $model && $pw)
        //      $model = self::loadModelFromWebdb($id, $pw);

        if(! $model) {
            Yii::$app->session->addFlash('error', "入力された適用書NO、パスワードは該当ありません");
            return;
        }
        if($model->isExpired()){
            Yii::$app->session->addFlash('error', "この適用書は購入済みか、有効期限が過ぎています");
            return;
        }
        return $model;
    }

    /**
     * get recipe from webdb:
     * firstly check webdb18, then webdb20 may be seeked.
     */
    private static function loadModelFromWebdb($id,$pw)
    {
        $model = (new \common\models\webdb\RecipeFinder())->get(['id'=>$id,'pw'=>$pw]);

        if(! $model)
            throw new \yii\web\NotFoundHttpException("当該のNOまたはパスワードが一致しません");

        if(! $model->save(false))
        {
            Yii::error(['failed to import recipe', $model->attributes, $model->errors], self::className().'::'.__FUNCTION__);
            return $model;
        }

        return \common\models\Recipe::findOne($model->recipe_id);
    }

}
