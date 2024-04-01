<?php

namespace frontend\modules\pointing;

use Yii;
use \common\models\WtbPointing;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/pointing/Module.php $
 * $Id: Module.php 3606 2017-09-24 05:55:37Z naito $
 */

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'frontend\modules\pointing\controllers';
    public $pointForm;

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        if(in_array($action->id, ['create','update','apply','finish']))
            $this->loadPointForm();

        return true;
    }

    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);

        if(in_array($action->id, ['create','update','apply']))
            $this->savePointForm();

        return $result;
    }

    public static function getName()
    {
        return "販売店・取扱所様専用売上入力";
    }

    private function loadPointForm()
    {
        $this->pointForm = new \common\models\PointingForm([
            'seller_id'  => Yii::$app->user->id,
            'company_id' => $this->findCompany()->company_id,
        ]);
        
        $buf = $this->loadBuffer();

        if($buf)
            $this->pointForm->feed($buf);
        
        if(0 < $this->pointForm->pointing_id)
        {
            $model = \common\models\PointingForm::findOne($this->pointForm->pointing_id);
            if($model) // ! isNewRecord
            {
                // swap the model (so that pkey can validate safely)
                $this->pointForm = $model;
                $this->pointForm->feed($buf); // update content
            }
        }
    }

    public function reLoadBuffer()
    {
        $buf       = $this->loadBuffer();
        
        if($buf && ($this->pointForm->pointing_id == \yii\helpers\ArrayHelper::getValue($buf,'pointing_id')))
            $this->pointForm->feed($buf);
    }

    private function savePointForm()
    {
        $buf = $this->pointForm->dump();

        $this->saveBuffer($buf);
    }

    /* @return array */
    private function loadBuffer()
    {
        $row = WtbPointing::findOne(['session' => Yii::$app->session->id]);
        if(! $row)
            return [];

        $data = json_decode($row->data, true); // convert to array

        $company = $this->findCompany();

        if(! isset($data[$company->key]))
            $dump = [];
        else
            $dump = $data[$company->key];

        return $dump;
    }

    private function saveBuffer($dump)
    {
        $row = WtbPointing::findOne(['session' => Yii::$app->session->id]);
        if($row)
            $data = json_decode($row->data, true); // convert to array
        else
        {
            $row  = new WtbPointing(['session' => Yii::$app->session->id]);
            $data = [];
        }

        $company = $this->findCompany();
        $data[$company->key] = $dump; // set the dump in array

        $row->data = json_encode($data);

        return $row->save();
    }

    public function clearBuffer()
    {
        $row = WtbPointing::findOne(['session' => Yii::$app->session->id]);
        if(! $row)
        {
            Yii::warning(['there was no such buffer for the session',
                          'pointing_id' => $this->pointForm ? $this->pointForm->pointing_id : null,
                          'url'         => Yii::$app->request->absoluteUrl,
                          'ip'          => Yii::$app->request->userIp,
                          'user'        => Yii::$app->user->id,
            ],static::className().'::'.__FUNCTION__);

            return true;
        }
        $data = json_decode($row->data, true); // convert to array

        $data[$this->pointForm->company->key] = []; // clear buffer

        $row->data = json_encode($data); // save buffer (the data might contain other company's buffer)

        return $row->save();
    }

    private static function findCompany()
    {
        $company = \common\models\Company::findOne(['key'=>Yii::$app->controller->id]);
        if(! $company)
            throw new \yii\web\BadRequestHttpException('controller->id does not match');

        return $company;
    }
}
