<?php

namespace backend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use common\models\RemedyDescription;
use common\models\SearchRemedyDescription;
use common\models\Remedy;
use common\models\RemedyCategory;

/**
 */
class RemedyDescriptionController extends BaseController
{

    /**
     * 事前処理チェック・パンくず作成
     *
     * @param unknown $action
     * @return boolean
     */
    public function beforeAction($action)
    {
        if (! parent::beforeAction ( $action ))
            return false;

        $this->view->params['breadcrumbs'][] = ['label' => 'レメディー', 'url' => ['/remedy/index']];
        return true;
    }

    /**
     * レメディ商品の補足一覧
     */ 
    public function actionIndex()
    {
        $searchModel = new SearchRemedyDescription();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * レメディ商品の補足説明詳細
     *
     * @param integer $id 補足ID
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id)
        ]);
    }

    /**
     * レメディ商品の補足説明追加
     *
     * @param intger $remedy_id
     *            レメディー商品ID
     */
    public function actionCreate($remedy_id)
    {
        $remedy = Remedy::findOne ( $remedy_id );
        if (! $remedy)
            throw new BadRequestHttpException ( splintf ( 'Invalid remedy_id: %s', $remedy_id ) );

        $model = new RemedyDescription ();
        $model->remedy_id = $remedy->remedy_id;

        if (Yii::$app->request->isPost) {

            $model->load ( Yii::$app->request->post () );

            // 説明区分が「広告」の場合、カテゴリーIDを0に表示順はデフォルトに設定
            if ($model->isAd ()) {
                $model->title = null; // DB上に見出しが登録されていた場合でも初期化する（バックヤードでの混乱を防ぐため）
            }

            if ($model->save ()) {
                // 処理成功時
//                 Yii::$app->session->addFlash ( 'success', sprintf ( '%s の補足説明「%s」を登録しました。', $model->remedy->abbr, $model->title ) );
                return $this->redirect(['/remedy/view', 'id' => $remedy->remedy_id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * レメディ商品の補足説明更新
     *
     * @param integer $id 補足ID
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel ( $id );

        if (Yii::$app->request->isPost) {

            $model->load ( Yii::$app->request->post () );

            // 説明区分が「広告」の場合、カテゴリーIDを0に表示順はデフォルトに設定
            if ($model->isAd ()) {
                $model->title = null; // DB上に見出しが登録されていた場合でも初期化する（バックヤードでの混乱を防ぐため）
            }

            if ($model->save ()) {
                return $this->redirect(['remedy/view', 'id' => $model->remedy_id]);
            }
        }

        return $this->render('update', [
            'model' => $model
        ]);
    }

    /**
     * レメディ商品の補足説明削除
     *
     * @param integer $id 補足ID
     */
    public function actionDelete($id)
    {
        $model = $this->findModel ( $id );
        $model->delete ();

        return $this->redirect(['remedy/view', 'id' => $model->remedy_id]);
    }

    /**
     * レメディ補足説明IDをもとにRemedyDescriptionのmodelを取得する
     * modelが取得できない場合は404 HTTP exceptionを投げる。
     *
     * @param integer $id
     * @return RemedyDescription the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($r_desc_id) {
        if (! $model = RemedyDescription::findOne ( $r_desc_id ))
            throw new NotFoundHttpException ( 'The requested page does not exist.' );

        return $model;
    }
}