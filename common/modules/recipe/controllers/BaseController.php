<?php

namespace common\modules\recipe\controllers;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/recipe/controllers/BaseController.php $
 * $Id: BaseController.php 3951 2018-06-29 07:21:54Z mori $
 */
use Yii;
use yii\helpers\Html;
use common\models\RecipeForm;

abstract class BaseController extends \yii\web\Controller
{
    public $nav;

    public $crumbs;

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        return true;
    }

    /**
     * Updates an existing Recipe model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $force=false)
    {
        $model = $this->findModel($id);

        if(! $model->isExpired() && (false === $force))
        {
            Yii::$app->session->addFlash('warning',"<p>再作成すると、この適用書は無効になります。よろしいですか。</p>"
                                                  .\yii\helpers\Html::a('はい',['update','id'=>$id,'force'=>'true']));
            return $this->redirect(['view','id'=>$id]);
        }
        if(! $model->isExpired())
             $model->expire();

        $this->copy($id);

        return $this->redirect(['create/index']);
    }

    public function actionUpdatekeepsts($id, $confirm=false)
    {
        $this->copy($id, true);

        $model = $this->findModel($id);

        if(false === $confirm)
        {
            Yii::$app->session->addFlash('warning',"<p>この適用書をコピーし、新たに適用書を作成します。よろしいですか。</p>"
                                                  .\yii\helpers\Html::a('はい',['updatekeepsts','id'=>$id,'confirm'=>'true'])
                                                  .'&nbsp;&nbsp;&nbsp;'
                                                  .\yii\helpers\Html::a('いいえ',['view','id'=>$id]));
            return $this->redirect(['view','id'=>$id]);
        }

        return $this->redirect(['create/index']);
    }

    private function copy($recipeId, $default=true)
    {
        $recipe = RecipeForm::findOne($recipeId);
        $recipe->recipe_id = null;
        $recipe->pw = null;
        $recipe->status = $recipe::STATUS_INIT;
        if (! $default) {
            $recipe->client_id = null;
            $recipe->manual_client_name = null;
            $recipe->manual_client_age = null;
            $recipe->manual_protector_name = null;
            $recipe->manual_protector_age = null;
        }
        $row    = \common\models\WtbRecipe::fetchOne(Yii::$app);
        $data   = [
            'recipeForm' => $recipe->dump(),
            // 'client'     => $recipe->client->attributes,
        ];
        $row->data = json_encode($data);
        if(! $row->save())
            Yii::error(['WtbRecipe::save() failed', $recipe->attributes, $recipe->errors]);
    }

    public function actionUpdateedit($id, $confirm=false)
    {
        $this->load($id, true);

        $model = $this->findModel($id);

        if(false === $confirm)
        {
            Yii::$app->session->addFlash('warning',"<p>この適用書を本当に編集しますか？</p>"
                                                  .\yii\helpers\Html::a('はい',['updateedit','id'=>$id,'confirm'=>'true'])
                                                  .'&nbsp;&nbsp;&nbsp;'
                                                  .\yii\helpers\Html::a('いいえ',['view','id'=>$id]));
            return $this->redirect(['view','id'=>$id]);
        }

        return $this->redirect(['create/index']);
    }

    private function load($recipeId)
    {
        $recipe = RecipeForm::findOne($recipeId);
        $row    = \common\models\WtbRecipe::fetchOne(Yii::$app);
        $data   = [
            'recipeForm' => $recipe->dump(),
            // 'client'     => $recipe->client->attributes,
        ];
        $row->data = json_encode($data);
        if(! $row->save())
            Yii::error(['WtbRecipe::save() failed', $recipe->attributes, $recipe->errors]);
    }

}
