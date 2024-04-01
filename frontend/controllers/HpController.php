<?php
namespace frontend\controllers;
use Yii;

/**
 * Site controller
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/controllers/HpController.php $
 * $Id: HpController.php 4067 2018-11-28 08:10:14Z kawai $
 */
class HpController extends CompanyController
{
    function actionIndex()
    {
        // HPならリダイレクトさせる
        if($this->company->company_id == \common\models\Company::PKEY_HP) {
            $this->redirect(['../']);
        }
    }

    function actionProduct()
    {
        // HPの商品画面に来た場合は例外処理にする
        throw new \yii\web\NotFoundHttpException('書籍の取り扱いは終了しました。');
    }
}
