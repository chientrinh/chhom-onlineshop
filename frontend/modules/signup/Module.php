<?php

namespace frontend\modules\signup;

use Yii;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'frontend\modules\signup\controllers';
    public $srcCompany;
    public $srcCustomer;
    public $dstCustomer;
    public $wtbCustomer;
    public $finder;
    public $matrix;

    public function init()
    {
        parent::init();

        $this->matrix = [
            'ty' => ['member'  => "とようけ会",
                     'message' => "オンラインショップでご利用中のIDとパスワードを入力してください"],

            'hj' => ['member'  => "自然の会",
                     'message' => "オンラインショップでご利用中のIDとパスワードを入力してください"],

            'he' => ['member'  => "CHhom、JPHMA および関連各社",
                     'message' => "ご案内したIDとパスワードを入力してください"],

            'hp' => ['member'  => "ホメオパシー出版",
                     'message' => "オンラインショップでご利用中のIDとパスワードを入力してください"],

            'ecorange' => [
                'member'  => "豊受オーガニクスショップ会員",
                'message' => "オンラインショップでご利用中のIDとパスワードを入力してください"
            ],

        ];

        $this->finder = new models\CustomerFinder();
    }

    public function beforeAction($action)
    {
        if (! parent::beforeAction($action))
            return false;

        if(in_array($action->id, ['search','update']))
            $this->loadSession();

        return true;
    }

    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);

        if(Yii::$app->request->isPost &&
           in_array($action, ['search','update'])
        )
            $this->saveBuffer();

        return $result;
    }

    /**
     * append migrative properties after Customer::saave()
     * @return void
     */
    public function appendAttributesAfterSave($customer)
    {
        \common\components\CustomerMigration::syncModel($customer, $this->srcCustomer);

        $srcs = \common\components\CustomerMigration::findModels($customer);

        foreach($srcs as $src)
        {
            if(($src->customerid == $this->srcCustomer->customerid) && 
               ($src->schema     == $this->srcCustomer->schema))
                   continue;

            \common\components\CustomerMigration::syncModel($customer, $src);
        }
    }

    /* @return void */
    private function loadSession()
    {

        $token = Yii::$app->getSession()->get('token', null);
        if($token)
        {
            $row = \common\models\WtbCustomer::find()->where([
                'token'=> $token,
            ])//->andWhere('expire >= NOW()')
                 ->one();
            if($row)
                $this->wtbCustomer = $row;
        }

        $this->loadModels();
    }

    private function saveSession()
    {
        if(! $this->srcCustomer || ! $this->srcCompany)
            return false; // no need to save

        $row = \common\models\WtbCustomer::find()->where(['token'=>Yii::$app->getSession()->get('token')])->one();
        if(! $row)
            $row = new \common\models\WtbCustomer();

        // overwrite data
        $row->data  = json_encode([
            'srcCustomer'=> $this->srcCustomer->attributes,
            'srcCompany' => ['company_id' => $this->srcCompany->company_id],
        ]);

        if($row->save())
        {
            $this->wtbCustomer = $row;
            Yii::$app->getSession()->set('token', $row->token);
            return true;
        }

        Yii::error('wtb_customer save() failed:' . json_encode($row->errors));
        return false;
    }

    /* @return bool */
    private function loadModels()
    {
        if($this->wtbCustomer)
            $data = json_decode($this->wtbCustomer->data, true);

        $company = \common\models\Company::find()->where(['key'=>Yii::$app->controller->id])->one();
        if(! $company)
        {
            if($this->wtbCustomer)
                    $this->wtbCustomer->delete();
            throw new \yii\web\BadRequestHttpException("提携社名が一致しませんでした。最初からやり直してください");
        }

        if(isset($data['srcCompany']))
        {
            $company_id = $data['srcCompany']['company_id'];
            if($company_id != $company->company_id)
            {
                if($this->wtbCustomer)
                    $this->wtbCustomer->delete();
                throw new \yii\web\BadRequestHttpException("提携社名が一致しませんでした。最初からやり直してください");
            }
        }
        $this->srcCompany = $company;

        $this->finder     = new models\CustomerFinder(['company'=>$this->srcCompany]);

        if(isset($data['srcCustomer']))
            $this->srcCustomer = $this->finder->loadModel($data['srcCustomer']);

        if($this->srcCustomer)
        {
            $this->dstCustomer = $this->srcCustomer->migrateAttributes();
            return true;
        }

        return false;
    }

    /* @return bool */
    public function searchModel($param)
    {
        $this->finder = new models\CustomerFinder(['company'=>$this->srcCompany]);

        if(! $this->finder->load($param))
            return false;

        if(! $this->finder->validate())
            return false;

        $this->srcCustomer = $this->finder->search();
        if(! $this->srcCustomer)
            return false;

        $this->saveSession();

        return true;
    }

}
