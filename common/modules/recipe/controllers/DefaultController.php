<?php

namespace common\modules\recipe\controllers;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/recipe/controllers/DefaultController.php $
 * $Id: DefaultController.php 3851 2018-04-24 09:07:27Z mori $
 */
use Yii;
use common\models\Recipe;
use yii\helpers\Html;

class DefaultController extends BaseController
{
    public $crumbs = [
        'index' =>['label'=>"履歴",],
        'view'  =>['label'=>"閲覧",],
    ];

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->nav = \frontend\widgets\Nav::begin([
            'items'   => [
                ['label' => "履歴",    'url' => ['/recipe/default/index'], ],
                ['label' => "作成",    'url' => ['/recipe/create/index'], ],
                ['label' => "閲覧",    'url' => ['/recipe/default/view'], ],
            ],
        ]);

        return true;
    }

    public function actionIndex($client=null)
    {
        $provider = $this->loadProvider();
        $provider->query->andFilterWhere(['client_id'=>Yii::$app->request->get('client')]);

        return $this->render('index', ['dataProvider'=>$provider]);
    }

    public function actionView($id = 0)
    {
        if(0 === $id)
            return $this->redirect('index');

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

    public function actionExpire($id)
    {
        $model = $this->findModel($id);

        if(! $model->expire())
            Yii::error($model->errors, self::className().'::'.__FUNCTION__);

        return $this->redirect(['view','id'=>$model->recipe_id]);
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

    protected function findModel($id)
    {
        if(null === ($model = Recipe::findOne($id)))
           throw new \yii\web\NotFoundHttpException('当該IDは見つかりません');

        if(Yii::$app->user->id !== $model->homoeopath_id)
           throw new \yii\web\NotFoundHttpException('自分が発行した適用書のみ閲覧できます');
        
        return $model;
    }

    private function loadProvider()
    {
        $query = Recipe::find()->where([
            'homoeopath_id' => Yii::$app->user->id,
        ]);

        return new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort'  => [
                'attributes' => [
                    'create_date',
                    'recipe_id',
                    'status',
                ],
                'defaultOrder' => ['recipe_id' => SORT_DESC],
            ],
        ]);
    }

}
