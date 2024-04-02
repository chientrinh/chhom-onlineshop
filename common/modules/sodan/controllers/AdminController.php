<?php

namespace common\modules\sodan\controllers;

use Yii;
use \yii\helpers\Url;
use \common\models\sodan\Interview;
use \common\models\Branch;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/controllers/AdminController.php $
 * $Id: AdminController.php 3890 2018-05-23 06:19:37Z mori $
 */

class AdminController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $center_branch_id = null;
        if (Yii::$app->id === 'app-backend') {
            $staff_role = (new \yii\db\Query())->select(['*'])->from('mtb_staff_role')->where(['staff_id' => Yii::$app->user->identity->attributes['staff_id']])->one();
            $branch_id = ($staff_role) ? $staff_role['branch_id'] : '';
            $center_branch = Branch::find()->center()->andwhere(['branch_id' => $branch_id])->one();
            $center_branch_id = ($center_branch) ? $branch_id : '';
        }
        return $this->render('index', [
            'branch_id' => $center_branch_id
        ]);
    }

    public function actionCreateRecipe($id)
    {
        if(! $model = Interview::findOne($id))
            throw new \yii\base\UserException("相談会ID {$id} は存在しません");

        $this->mockRecipe($model);

        Url::remember(Url::to(['interview/view','id'=>$id]), 'recipe');

        return $this->redirect(['/recipe/create/index']);
    }

    public function actionRebuildRecipe($id)
    {
        if(! $model = Interview::findOne($id))
            throw new \yii\base\UserException("相談会ID {$id} は存在しません");

        if(($recipe = $model->recipe) && ! $recipe->expire())
            throw new \yii\db\IntegrityException("エラーが発生しました：適用書を無効にできません。システム担当者にお問い合わせください");
        if($recipe)
            $this->copyRecipe($recipe);

        Url::remember(Url::to(['interview/view','id'=>$id]), 'recipe');

        return $this->redirect(['/recipe/create/index']);
    }

    private function copyRecipe(\common\models\Recipe $recipe)
    {
        $recipe = \common\models\RecipeForm::findOne($recipe->recipe_id);
        $row    = \common\models\WtbRecipe::fetchOne(Yii::$app);
        $recipe->status = $recipe::STATUS_INIT;
        $data   = [
            'recipeForm' => $recipe->dump(),
            'client'     => $recipe->client->attributes,
        ];
        $row->data = json_encode($data);
        if(! $row->save())
            Yii::error(['WtbRecipe::save() failed', $recipe->attributes, $recipe->errors]);
    }

    private function mockRecipe(\common\models\sodan\Interview $model)
    {
        // mockup WtbRecipe, to be read by recipe/Module
        $recipe = new \common\models\RecipeForm([
            'homoeopath_id' => $model->homoeopath_id,
            'client_id'     => $model->client_id,
            'center'        => $model->branch->name,
            'tel'           => $model->branch->tel
        ]);
        $row = \common\models\WtbRecipe::fetchOne(Yii::$app);
        $data = [
            'recipeForm' => $recipe->dump(),
            'client'     => $model->client->attributes,
        ];
        $row->data = json_encode($data);
        if(! $row->save())
            Yii::error(['WtbRecipe::save() failed', $recipe->attributes, $recipe->errors]);
    }

}
