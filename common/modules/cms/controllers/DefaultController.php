<?php

namespace common\modules\cms\controllers;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/cms/controllers/DefaultController.php $
 * $Id: DefaultController.php 1990 2016-01-22 04:27:10Z mori $
 */
use Yii;

class DefaultController extends \yii\web\Controller
{
    public function actionView($id, $page)
    {
        $fullpath = $this->findFile($id, $page);

        $response = \Yii::$app->getResponse();
        $response->sendFile($fullpath, $page, ['inline'=>true]);
        return $response->send();
    }

    private function findFile($id, $page)
    {
        if(! $page)
             $page = 'index.html';

        $fullpath = sprintf('%s/%s/%s/%s',
                            Yii::getAlias('@common/content'),
                            $this->id,
                            $id,
                            $page);

        if(! is_readable($fullpath))
            throw new \yii\web\NotFoundHttpException('ページが見つかりません。');

        return $fullpath;
    }
}
