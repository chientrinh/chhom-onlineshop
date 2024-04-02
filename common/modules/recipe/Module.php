<?php

namespace common\modules\recipe;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/recipe/Module.php $
 * $Id: Module.php 3946 2018-06-22 04:07:17Z mori $
 */

use Yii;
use \common\models\WtbRecipe;
use \common\models\Customer;
use \backend\models\Staff;

class Module extends \yii\base\Module
{
    public $client;
    public $controllerNamespace = 'common\modules\recipe\controllers';
    public $recipeForm;

    public function init()
    {
        parent::init();

        Yii::$app->formatter->nullDisplay = '<span class="not-set">(なし)</span>';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'cache' => [
                'class' => 'yii\filters\HttpCache',
                'only' => ['index','view'],
                'lastModified' => function ($action, $params)
                {
                    $q = new \yii\db\Query();
                    return strtotime($q->from('dtb_recipe')->max('update_date'));
                },
            ],
            'access' => [
                'class'      => \yii\filters\AccessControl::className(),
                'ruleConfig' => [ // default configration for every rules
                    'class'       => \yii\filters\AccessRule::className(),
                    'controllers' => ['recipe/create','recipe/admin','recipe/default'],
                    'roles'       => ['@'],
                    'allow'       => true,
                ],
                'rules' => [
                   [
                       'controllers' => ['recipe/review'],
                       'roles' => ['?','@'],
                   ],
                   [
                       'allow' => false,
                       'roles' => ['?'], // deny guest users
                   ],
                   [ // allow Homoeopathes @frontend
                       'matchCallback' => function()
                       {
                           $user = Yii::$app->user->identity;
                           return (($user instanceof Customer) &&
                                   ($user->isHomoeopath() || $user->isStudent() || $user->isJphmatechnical()));
                       },
                   ],
                   [ // allow everyone @backend
                       'matchCallback' => function()
                       {
                           $user = Yii::$app->user->identity;
                           return $user instanceof Staff && $user->hasRole(['worker','manager','wizard']);
                       },
                   ],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        $new = isset(Yii::$app->request->get()['new']) ? true : false;

        if(! parent::beforeAction($action))
            return false;

        if('create' == Yii::$app->controller->id)
            $this->initRecipeForm($new);
        else
            $this->initviewParams($action);

        return true;
    }

    private function initViewParams($action)
    {
        Yii::$app->controller->view->params['breadcrumbs'][] = ['label'=>'適用書','url'=>['/recipe']];
    }

    private function defaultHomoeopathId()
    {
        $app = Yii::$app;

        if('app-frontend' == $app->id)
            return Yii::$app->user->id;

        elseif('app-backend' == $app->id)
            return 0 - abs(Yii::$app->user->id);

        return null;
    }

    private function initRecipeForm($new = false)
    {
        $user = Yii::$app->user->identity;
        $cid  = null;
        $hid  = $user instanceof \common\models\Customer ? $user->id : null;
        $center = null;

        if($user instanceof \common\models\Customer &&
            (! $user->isHomoeopath() && $user->isStudent())
        )
            $cid = $user->id;


        if($new)
            WtbRecipe::removeOne(Yii::$app);

        $data = $this->loadWtbRecipe();

        if(!$data["recipeForm"]["recipe_id"]){
            // 提携施設＝センター名の取得。ただしホメオパスのみ
            if($hid)
                $center = \common\models\Facility::findOne(['customer_id' => $hid]);

            $this->recipeForm = new \common\models\RecipeForm([
                'homoeopath_id' => $hid,
                'client_id'     => $cid,
                'center'        => $center ? $center->name : null,
            ]);
        } else {
            $this->recipeForm = \common\models\RecipeForm::findOne($data["recipeForm"]["recipe_id"]);
        }

        if($data && array_key_exists('recipeForm', $data))
            $this->recipeForm->feed($data['recipeForm']);

        if($data && array_key_exists('client', $data))
            $this->client = new \common\models\Customer($data['client']);
        else
            $this->client = new \common\models\Customer(['customer_id' => $cid]);

    }

    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);

        if('create' == Yii::$app->controller->id)
            $this->saveModel();

        return $result;
    }

    public static function getName()
    {
        return "適用書";
    }

    public function setClient(\common\models\Membercode $model)
    {
        if($model->customer_id)
        {
            $this->client = $model->customer;
            $this->recipeForm->client_id = $model->customer_id;
            return true;
        }

        $customer = \common\components\CustomerMigration::migrateModel($model);

        if(! $customer instanceof \common\models\Customer)
        {
            Yii::error("CustomerMigration::migrateModel({$model->code}) failed");
            Yii::$app->session->addFlash('error', sprintf("その人は豊受モールへの移行手続が完了していません(%d)",$model->migrate_id));
            return false;
        }

        $this->client = $customer;
        $this->recipeForm->client_id = $model->customer_id;

        return true;
    }

    public function insertRecord()
    {
        if(! $this->recipeForm->save())
            return false;

        if(WtbRecipe::removeOne(Yii::$app))
            Yii::$app->session->addFlash('success',"適用書を保存しました");
        else
            Yii::error('WtbRecipe::deleteOne() failed');

        return true;
    }

    /* @return array or null */
    private function loadWtbRecipe()
    {
        $row = WtbRecipe::fetchOne(Yii::$app);

        if(Yii::$app->session->id != $row->session)
        {
            // homoeopath has accessed from another device, switch the session ID
            $row->session = Yii::$app->session->id;
            $row->save();
        }
        $data = json_decode($row->data, true); // convert to array

        return $data;
    }

    private function saveModel()
    {
//        if(! $this->recipeForm->isNewRecord) // was inserted into dtb_recipe
//            return true;

        $row = WtbRecipe::fetchOne(Yii::$app);

        $data = [
            'recipeForm' => $this->recipeForm->dump(),
            'client'     => $this->client->attributes,
        ];
        $row->data = json_encode($data);
        return $row->save();
    }
}
