<?php

namespace backend\controllers;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/BarcodeController.php $
 * $Id: BarcodeController.php 2840 2016-08-12 08:47:12Z mori $
 */

use Yii;

class BarcodeController extends \yii\web\Controller
{
    const PATH_TEMPLATE = '@runtime/%s.%s';

    public function actionIndex()
    {
        $code   = Yii::$app->request->get('code', '9784946572654');
        $label  = Yii::$app->request->get('label', null);
        $format = Yii::$app->request->get('format','png');

        return $this->render('index', [
            'code'   => $code,
            'label'  => $label,
            'format' => $format,
        ]);
    }

    public function actionDraw($code, $label=null, $format='png')
    {
        $fullpath = Yii::getAlias(sprintf(self::PATH_TEMPLATE, $code, $format));

        \common\components\pommespanzer\barcode\Barcode::run($code, $fullpath, [
            // 'type'  => 'ean13', 'ean13' will be applied if (13 == strlen($text))
            'format'=> $format,
            'dpi'   => 72,
            'label' => $label,
        ]);
        
        $response = \Yii::$app->getResponse();
        $response->sendFile($fullpath, basename($fullpath),['inline'=>true]);
        return $response->send();
    }

    public function actionView($id)
    {
        return $this->actionDraw($id);
    }

}
