<?php

namespace common\modules\cms\controllers;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/cms/controllers/OasisController.php $
 * $Id: OasisController.php 2921 2016-10-05 06:47:10Z mori $
 */
use Yii;

class OasisController extends DefaultController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],

                        'matchCallback' => function ($rule, $action)
                        {
                            $user = Yii::$app->user->identity;
                            return $user && (
                                $user instanceof \backend\models\Staff ||
                                $user->isToranoko()
                            );
                        },
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $pattern = sprintf('%s/%s/*/index.html',
                           Yii::getAlias('@common/content'), $this->id);
        $files   = glob($pattern);

        $models = [];
        foreach($files as $file)
        {
            $dir  = basename(dirname($file));
            $base = basename($file);

            $pattern = sprintf('%s/%s/%s/*.pdf',
                               Yii::getAlias('@common/content'), $this->id, $dir);
            if($pdfs = glob($pattern))
                $pdf = array_shift($pdfs);
            else
                $pdf = '';

            $models[(int)$dir] = (object)[
                'id'   => $dir,
                'page' => $base,
                'pdf'  => basename($pdf),
                'url'  => ['view', 'id'=>$dir, 'page'=>$base],
            ];
        }

        return $this->render('index', ['models' => $models]);
    }

}
