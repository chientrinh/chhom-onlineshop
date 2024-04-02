<?php

namespace common\modules\recipe\controllers;

use Yii;

use \common\models\Recipe;
use \common\models\RecipeForm;
use \common\models\SearchRecipe;

/**
 * RecipeController implements the CRUD actions for Recipe model.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/recipe/controllers/AdminController.php $
 * $Id: AdminController.php 4091 2018-12-26 08:49:06Z kawai $
 */

class AdminController extends BaseController
{
    public $crumbs = [
        'create' =>['label'=>"作成",'url'=>'index'],
        'view'   =>['label'=>"表示",],
    ];

    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [// allow everyone @backend
                     'allow'         => true,
                     'roles'         => ['@'],
                     'matchCallback' => function()
                     {
                        $center_homoeopath = \common\models\CustomerMembership::find()->active()->where(['customer_id' => Yii::$app->user->id, 'membership_id' => \common\models\Membership::PKEY_CENTER_HOMOEOPATH])->one();
                        $sodan_homoeopath = \common\models\sodan\Homoeopath::find()->active()->andWhere(['homoeopath_id' => Yii::$app->user->id])->one();
                        $user = Yii::$app->user->identity;
                        return ($user instanceof \backend\models\Staff || ($center_homoeopath && $sodan_homoeopath));
                     }
                    ],
                ],
            ]
        ];
    }

    /**
     * Lists all Recipe models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchRecipe();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Recipe model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id,$preview=false)
    {
        $model = $this->findModel($id);

        if(false === $preview)
            return $this->render('view', [
                'model' => $model,
            ]);

        $this->layout = false;
        $html = \common\widgets\doc\recipe\RecipeDocument::widget([
            'model' => $model,
        ]);
        return $this->renderContent($html);
    }

    /**
     * Displays a single Recipe model.
     * @param integer $id
     * @return mixed
     */
    public function actionPreiew($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionPrint($id, $format='html')
    {
        $this->layout = false;

        $html = \common\widgets\doc\recipe\RecipeDocument::widget([
            'model' => $this->findModel($id),
        ]);

        if('pdf' == $format)
            return $this->renderPdf($html);

        return $this->renderContent($html);
    }

    protected function renderPdf($html)
    {
        $pdf  = \common\components\mPdf::mPdf('ja+aCJK');

        define('_MPDF_SYSTEM_TTFONTS',"@vendor/kartik-v/mpdf/ttfonts/");
        $JpFontName = 'ipapgothic';
        $pdf->fontdata[$JpFontName] = array(
                'R' => 'ipagp.ttf',
        );
        $pdf->available_unifonts[] = $JpFontName;
        $pdf->default_available_fonts[] = $JpFontName;
        $pdf->BMPonly[] = $JpFontName;
        $pdf->SetDefaultFont($JpFontName);


        $pdf->WriteHtml($html);
        $pdf->Output();

        return;
    }
    /**
     * Creates a new Recipe model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RecipeForm();

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * revert to INIT status
     * @param integer $id
     * @return mixed
     */
    public function actionReset($id)
    {
        $model = $this->findModel($id);
        if($model->manual_client_name)
            $model->status = $model::STATUS_PREINIT;
        else
            $model->status = $model::STATUS_INIT;

        $model->save();

        return $this->redirect(['view','id'=>$id]);
    }

    /**
     * Deletes an existing Recipe model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->expire();

        return $this->redirect(['view','id'=>$id]);
    }

    /**
     * Finds the Recipe model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Recipe the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if(! $model = Recipe::findOne($id))
            throw new \yii\web\NotFoundHttpException('ページが見つかりません');

        return $model;
    }
}
