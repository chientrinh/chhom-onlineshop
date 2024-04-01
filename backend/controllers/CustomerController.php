<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/CustomerController.php $
 * $Id: CustomerController.php 4175 2019-07-24 03:00:11Z mori $
 */

namespace backend\controllers;

use Yii;
use common\models\Membercode;
use common\models\Purchase;
use backend\models\Customer;
use backend\models\SearchCustomer;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Expression;

/**
 * CustomerController implements the CRUD actions for Customer model.
 */
class CustomerController extends BaseController
{

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label'=>'顧客','url'=>['index']];

        return true;
    }

    /**
     * Lists all Customer models.
     * @return mixed
     */
    public function actionIndex($grade=null, $company=null, $format='html')
    {
        $model = new SearchCustomer();
        $req   = Yii::$app->request;

        if($req->isPost)
        {
            $model->load($req->post());
            $format = $req->post('format', 'html');
        }
        else
            $model->load($req->queryParams);

        $provider = self::loadProvider($model);

        if('html' == $format)
            return $this->render('index', [
                'searchModel'  => $model,
                'dataProvider' => $provider,
            ]);

        if('csv' != $format)
            throw new \yii\web\NotFoundException();

        // allow to consume up to 1GB for this process
        ini_set("memory_limit","2G");
        ini_set('max_execution_time', 0);

        //対象の顧客全件の氏名と連絡先ををCSVに書き出す
        $output = sprintf('%s-%s.csv', $this->id, time());
        $inline = false;
        $mime   = 'text/csv';
        Yii::$app->response->setDownloadHeaders(basename($output), $mime, $inline);
        Yii::$app->response->send();

        \common\widgets\CustomerCsvView::widget([
            'query'      => $provider->query,
            'attributes' => [
                'customer_id',
                'grade.name',
                'zip',
                'addr',
                'name',
                'kana',
                'tel',
                'email',
                'subscription.name',
                'code',
                'hasParent',
                'hasChildren',
                'w20',
            ],
        ]);
        return;
    }

    public function actionPurchase($format='html')
    {
        $q1 = Customer::find()->active();
        $q2 = Purchase::find()->active()->groupBy('customer_id')->orderBy(['create_date'=>SORT_DESC]);

        $query = new \yii\db\Query();
        $query->select([
            'c.customer_id',
            'CONCAT(c.name01, c.name02) as customer_name',
            'p.purchase_id',
            'p.create_date',
        ])
              ->from(['c'=>'dtb_customer'])
              ->leftJoin(['p'=>'dtb_purchase'],'p.customer_id = c.customer_id')
              ->andWhere('p.customer_id = c.customer_id OR p.customer_id is NULL')
              ->andWhere('NOW() <= c.expire_date') // Customer::find()->active()
              ->orderBy(['p.create_date' => SORT_DESC])
              ->groupBy('c.customer_id');

        $provider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 50],
            'sort'  => [
                'attributes' => [
                    'customer_id',
                    'purchase_id',
                    'create_date',
                ],
                'defaultOrder' => ['create_date' => SORT_DESC],
            ],
        ]);

        if('html' == $format)
            return $this->render('purchase',['provider'=>$provider]);

        // allow to consume up to 1GB for this process
        ini_set("memory_limit","1G");

        //CSVに書き出す
        $output = sprintf('%s-%s.csv', $this->id, time());
        $inline = false;
        $mime   = 'text/csv';
        Yii::$app->response->setDownloadHeaders(basename($output), $mime, $inline);
        Yii::$app->response->send();

        \common\widgets\CsvView::widget([
            'query'      => $provider->query,
            'attributes' => [
                'create_date',
                'customer_id',
                'customer_name',
                'purchase_id',
            ],
        ]);
        return;
    }

    /**
     * Displays a single Customer model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if($model->parent){ $model->scenario = $model::SCENARIO_CHILDMEMBER; }

        $model->validate();

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Customer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($parent=null)
    {
        if($parent)
            $parent = $this->findModel($parent);

        $model = $this->initModel($parent);

        if(Yii::$app->request->isPost)
        {
            $model->load(Yii::$app->request->post());

            if('zip2addr' === Yii::$app->request->post('scenario'))
                $model->zip2addr();

            elseif($model->save() ||
                   (// validate very limited attributes only, then save()
                       $model->validate(['name01','name02','kana01','kana02','tel01','tel02','tel03', 'email']) && $model->save(false))
            ){
                if(isset($parent))
                    $parent->link('children', $model);

                $this->finishModel($model);

                // 相談会クライアント作成→顧客作成した場合はクライアント画面へ戻す
                if (Yii::$app->request->post('mode') === 'client') {
                    if (Yii::$app->request->post('target') === 'child') {
                        return $this->redirect(["create-child?parent_id={$model->customer_id}"]);
                    }
                    return $this->redirect(["/sodan/client/create?client_id={$model->customer_id}"]);
                } else if (Yii::$app->request->post('mode') === 'recipe') {
                    return $this->redirect(["attach-membercode?id={$model->customer_id}&mode=recipe"]);
                } else if (Yii::$app->request->post('mode') === 'student') {
                    if (Yii::$app->request->post('target') === 'child') {
                        return $this->redirect(["create-child?parent_id={$model->customer_id}"]);
                    }
                    return $this->redirect(["/student/create?customer_id={$model->customer_id}"]);
                }

                return $this->redirect(['view', 'id' => $model->customer_id]);
            }
        }

        return $this->render('create', [
            'model'  => $model,
            'parent' => $parent,
            'mode'   => (Yii::$app->request->get('mode')) ? : ''
        ]);
    }

    public function actionCreateChild($parent_id = null)
    {
        if(!$parent_id)
            throw new \yii\web\NotFoundHttpException('顧客番号が不明です');

        $model = new Customer([
            'scenario' => \common\models\Customer::SCENARIO_CHILDMEMBER
        ]);

        if($model->load(Yii::$app->request->post())) {
            if($model->save()) {
                $family = new \common\models\CustomerFamily([
                    'parent_id' => $parent_id,
                    'child_id'  => $model->customer_id,
                ]);

                if($family->save()) {
                    if (Yii::$app->request->post('continue_flg')) {
                        Yii::$app->session->addFlash('success', '家族会員を作成しました。');
                        return $this->redirect(["create-child?parent_id={$parent_id}"]);
                    } else {
                        return $this->redirect(["/sodan/client/create?client_id={$model->customer_id}"]);
                    }
                }
            }
        }

        return $this->render('create-child', [
            'model' => $model,
            'parent_id' => $parent_id
        ]);
    }

    public function actionMigrate($from, $id)
    {
        $src = $this->findMigrationModel($from, $id);
        $dst = \common\components\CustomerMigration::migrateModel($src);

        if($dst)
            Yii::$app->session->addFlash('success', sprintf('%s さんの移行が完了しました',$src->name));
        else
            Yii::$app->session->addFlash('error', sprintf('%s さんの移行は完了しませんでした',$src->name));
        if(! $dst)
            return $this->redirect(Yii::$app->request->referrer);

        return $this->redirect(['view','id'=>$dst->customer_id]);
    }

    public function actionAttachMembercode($id, $mcode = null, $pw = null)
    {
        $customer = $this->findModel($id);
        $prev     = $customer->membercode->code;
        if(13 == strlen($mcode))
        {
            $substr10 = substr($mcode, 2, 10);
            $mcode    = $substr10;
        }

        if(strlen($mcode) && strlen($pw))
        {
            $model = \common\models\Membercode::find()->where(['code'=>$mcode,'pw'=>$pw])->one();

            if( $model && \common\components\CustomerMigration::attachMembercode($customer, $model)) {
                if (Yii::$app->request->get('mode') === 'recipe') {
                    return $this->redirect(['/recipe/create/search?target=client']);
                }
                Yii::$app->session->addFlash('success', sprintf('会員証NOを更新しました(%s -> %s)', $prev, $mcode));
                return $this->redirect(['view', 'id' => $id]);
            }
            Yii::$app->session->addFlash('error',"会員証NO無効、またはPWが一致しません");
        }

        $model = new \common\models\Membercode(['code'=>$mcode, 'pw'=>$pw]);

        return $this->render('attach-membercode',['customer' => $customer, 'model' => $model, 'mode' => Yii::$app->request->get('mode')]);
    }

    /**
     * 誰かを親として家族会員になる
     */
    public function actionAdapt($id, $parent_id=null)
    {
        $model = $this->findModel($id);

        if($parent = $model->parent)
            throw new \yii\base\UserException("{$model->name}さんは{$parent->name}さんの家族会員です。二重に親を指定できません");

        if((null !== $parent_id) && ($parent = $this->findModel($parent_id)))
        {
            if($grand = $parent->parent)
                throw new \yii\base\UserException("指定した{$parent->name}さんは別人を親に持つ家族会員です。親になれません");

            $parent->adapt($model);
            $this->addInfo($id,        "{$parent->name}さんの家族会員になりました");
            $this->addInfo($parent_id, "{$model->name}さんが家族会員になりました");
            Yii::$app->session->addFlash('success',"{$model->name}さんは{$parent->name}さんの家族会員になりました");

            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->render('adapt', [
            'model' => $model,
        ]);
    }

    /**
     * 親子関係を解消する（家族会員が独立する）
     */
    public function actionIndependent($id)
    {
        $model = $this->findModel($id);

        if(! $parent = $model->parent)
            throw new \yii\base\UserException("{$model->name}さんの親会員が見つかりません（すでに独立しています）");

        $model->unlink('parent', $parent, true);
        $this->addInfo($id,         "{$parent->name}さん({$parent->id})の家族会員から独立しました");
        $this->addInfo($parent->id, "{$model->name}さん({$model->id})が家族会員から独立しました");

        // 住所を上書き
        $attr = $parent->getAttributes(['zip01','zip02','pref_id','addr01','addr02','tel01','tel02','tel03']);
        $model->load($attr,'');
        $model->save(false);

        $q = $model->getMemberships()
                   ->andWhere(['membership_id' => \common\models\Membership::PKEY_TORANOKO_FAMILY]);

        if($q->exists())
        {
            $mship = $q->one();
            $mship->expire(); // 家族会員を終了
            $this->addInfo($id, "とらのこ家族会員の資格が喪失しました");
        }

        Yii::$app->session->addFlash('success',"{$model->name}さんが{$parent->name}さんから独立しました");

        return $this->redirect(['view', 'id' => $model->customer_id]);
    }

    /**
     * 親子関係を入れ替える
     */
    public function actionSwap($id,$child_id=null)
    {
        $model = $this->findModel($id);

        if($child_id = Yii::$app->request->get('child_id', null))
        {
            if(Customer::swap($id, $child_id))
            {
                $this->addInfo($id,       "親会員から家族会員にになりました");
                $this->addInfo($child_id, "家族会員から親会員になりました");
                return $this->redirect(['view', 'id' => $model->customer_id]);
            }
        }

        return $this->render('swap', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Customer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $scenario=null)
    {
        $model = $this->findModel($id);
        $model->validate();

        if(in_array($scenario, array_keys($model->scenarios())))
            $model->scenario = $scenario;

        if(Yii::$app->request->isPost)
        {
            $model->load(Yii::$app->request->post());

            if('zip2addr' === Yii::$app->request->post('scenario'))
                $model->zip2addr();

            elseif($model->validate(['name01','name02','kana01','kana02','tel01','tel02','tel03', 'email']) && $model->save(false))
            {

                if (Yii::$app->request->post('campaign_code') === '0501') {
                    $customer_campaign = new \common\models\CustomerCampaign([
                        'customer_id' => $model->customer_id
                    ]);
                    $customer_campaign->save();
                }

                if(false == \backend\widgets\CartNav::hasContent())
                    return $this->redirect(['view', 'id' => $model->customer_id]);

                return $this->redirect(['casher/default/apply',
                                        'id'    => $model->customer_id,
                                        'target'=> 'customer']);
            }
        }

        return $this->render('update', ['model'=> $model]);
    }
    
    /**
     * 顧客統合処理
     */
    public function actionUniteCustomer()
    {
        if($post = Yii::$app->request->post()) {
            $error_flg = false;
            
            if ($post['old_customer_id'] && $post['new_customer_id']) {
                $old_customer = $this->findModel($post['old_customer_id']);
                $new_customer = $this->findModel($post['new_customer_id']);
            } else {
                Yii::$app->session->addFlash('error', "統合する顧客IDと統合先の顧客IDを入力してください");
                return $this->render('unite-customer', ['error_flg' => true]);
            }

            if ($old_customer && $old_customer->ysdAccount) {
                Yii::$app->session->addFlash('error', "統合元の顧客に口座振替データがあるため統合できません");
                $error_flg = true;
            }
            
            if ($old_customer && $old_customer->isExpired()) {
                Yii::$app->session->addFlash('error', "統合元の顧客が無効になっています");
                $error_flg = true;
            }
            
            if ($new_customer && $new_customer->isExpired()) {
                Yii::$app->session->addFlash('error', "統合先の顧客が無効になっています");
                $error_flg = true;
            }
            
            if ($post['unite_flg']) {
                // 購入情報・ポイント付与履歴を・適用書・相談会のデータ新顧客で更新する
                \common\models\Pointing::updateAll(['customer_id' => $new_customer->customer_id], 'customer_id = :old_customer_id', [
                    ':old_customer_id' => $old_customer->customer_id
                ]);
                \common\models\Purchase::updateAll(['customer_id' => $new_customer->customer_id], 'customer_id = :old_customer_id', [
                    ':old_customer_id' => $old_customer->customer_id
                ]);
                \common\models\Recipe::updateAll(['client_id' => $new_customer->customer_id], 'client_id = :old_customer_id', [
                    ':old_customer_id' => $old_customer->customer_id
                ]);
                \common\models\sodan\Interview::updateAll(['client_id' => $new_customer->customer_id], 'client_id = :old_customer_id', [
                    ':old_customer_id' => $old_customer->customer_id
                ]);
                // 旧顧客を無効にする
                $old_customer->expire();
                $info = new \backend\models\CustomerInfo([
                    'customer_id' => $old_customer->customer_id,
                    'content'     => "{$new_customer->customer_id}に統合されました"
                ]);
                $info->save();
                Yii::$app->session->addFlash('success', "顧客ID：{$old_customer->customer_id}を顧客ID：{$new_customer->customer_id}に統合しました");
                return $this->redirect(['view', 'id' => $new_customer->customer_id]);
            }
            
            return $this->render('unite-customer', [
                    'old_customer' => $old_customer, 
                    'new_customer' => $new_customer,
                    'error_flg'    => $error_flg
                ]);
        }
        return $this->render('unite-customer', ['error_flg' => false]);
    }

    public function actionExpire($id)
    {
        $model = $this->findModel($id);
        $model->expire();

        $info = new \backend\models\CustomerInfo([
            'customer_id' => $id,
            'content'     => '無効になりました',
        ]);
        $info->save();

        return $this->redirect(['view','id'=>$id]);
    }

    public function actionActivate($id)
    {
        $model = $this->findModel($id);
        $model->activate();

        $info = new \backend\models\CustomerInfo([
            'customer_id' => $id,
            'content'     => '有効になりました',
        ]);
        $info->save();

        return $this->redirect(['view', 'id' => $id]);
    }

    /* prepare Customer for INSERT */
    public function initModel($parent)
    {
        $model = new Customer(['grade_id'=>1]);

        if($parent)
            $model->scenario = Customer::SCENARIO_CHILDMEMBER;

        return $model;
    }

    /**
     * @brief set properties after INSERT of child model
     * @return void
     */
    public function finishModel($model)
    {
        if(! $parent = $model->parent)
            return;

        if(! $parent->isToranoko())
            return;

        $model->setAsToranokoFamilyMember($parent);
    }

    /**
     * Finds the Customer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Customer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = Customer::findOne($id);
        if(! $model)
            throw new \yii\base\UserException("customer_id:{$id} は見つかりません");

        if($model->parent)
            $model->scenario = Customer::SCENARIO_CHILDMEMBER;

        return $model;
    }

    protected function loadProvider($model)
    {
        $req   = Yii::$app->request;
        $m     = $req->get('membership');
        $c     = $req->get('company');
        $g     = $req->get('grade');
        $picky = $req->get('picky');
        $mailmaga = $req->get('mailmaga');
        $is_active = $req->get('is_active');

        $query = (new \yii\db\Query())
            ->from(['c' => 'dtb_customer'])
            ->select('c.customer_id');

        if(!isset($model->is_active)) {
            if(isset($is_active))
                $model->is_active = $is_active;
            else 
                $model->is_active = 1;
        }

        if($m || $c)
            $query->innerJoin(['m' => 'dtb_customer_membership'], 'm.customer_id = c.customer_id');

        if($m)
        {
            $query->andWhere(['m.membership_id' => $m])
                  ->andWhere('m.start_date < NOW()')
                  ->andWhere('NOW() < m.expire_date');

            if($picky and is_array($m)) // 全membership_idに一致するレコードに限定する
                $query->groupBy('c.customer_id')
                      ->having(['COUNT(c.customer_id)' => count($m)]);
        }

        if($mailmaga)
        {
            // hasParentな顧客レコードを除外する
            $query->leftJoin(['f' => 'dtb_customer_family'], 'f.child_id = c.customer_id')
                  ->andWhere(['f.child_id' => null]);

            if($mailmaga[0] == 1) {
                $query->andWhere(['not', ['email' => '']])
                       ->andWhere(['or',
                            ['subscribe'=> null],
                            ['in','subscribe', [$mailmaga[0],3]]
                        ]);

            } else if($mailmaga[0] == 2){
                $query->andWhere(['or',
                             ['subscribe'=> null],
                             ['subscribe' => 2],
                             ['and',
                                 ['not',['email' => '']],
                                 ['subscribe' => 3]
                             ]
                         ]);
            }

        }

        if($c)
            $query->innerJoin(['s' => 'mtb_membership'], 's.membership_id = m.membership_id')
                  ->innerJoin(['p' => 'mtb_company'], 'p.company_id = s.company_id')
                  ->andWhere(['p.company_id' => $c]);
        if($g)
            $model->grade_id   = $g;

        $query = \backend\models\Customer::find()
               ->andWhere(['dtb_customer.customer_id' => $query])
               ->andFilterWhere(['dtb_customer.customer_id' => $model->customer_id])
               ->andFilterWhere(['dtb_customer.grade_id'    => $model->grade_id])
               ->andFilterWhere(['dtb_customer.sex_id'      => $model->sex_id])
               ->andFilterWhere(['dtb_customer.subscribe'   => $model->subscribe])
               ->andFilterWhere(['dtb_customer.pref_id'     => $model->pref_id]);

        if(1 == $model->is_active) {
            $query->andFilterWhere(['>=', 'dtb_customer.expire_date' , new Expression('NOW()')]);
        } else if(2 == $model->is_active) {
            $query->andFilterWhere(['<', 'dtb_customer.expire_date' , new Expression('NOW()')]);
        }

        if($model->keywords && $model->validate('keywords') && ($items = explode(' ',$model->keywords)))
            foreach($items as $item) {
                // 半角「+」を付けた場合はメモを検索するようにする
                if (mb_substr($item, 0, 1) === '+') {
                    $query->andFilterWhere([
                        'or',
                        ['like', 'dtb_customer_info.content', mb_substr($item, 1)]
                    ])->joinWith('infos');
                } else {
                    $query->andFilterWhere([
                        'or',
                        ['like','dtb_customer.name01',$item],
                        ['like','dtb_customer.name02',$item],
                        ['like','dtb_customer.kana01',$item],
                        ['like','dtb_customer.kana02',$item],
                        ['like','dtb_customer.email',$item],
                        ['like','CONCAT(dtb_customer.tel01,dtb_customer.tel02,dtb_customer.tel03)',$item],
                        ['like','CONCAT(dtb_customer.zip01,dtb_customer.zip02)',                   $item],
                        ['like','mtb_membercode.code',$item],
                    ])->joinWith('membercode');
                }
            }

        if(isset($model->is_agency)) {
            $query->leftJoin('dtb_customer_membership', 'dtb_customer_membership.customer_id = dtb_customer.customer_id');

            if(2 == $model->is_agency) {

               $query->andFilterWhere(['in', 'dtb_customer_membership.membership_id', [\common\models\Membership::PKEY_AGENCY_HJ_A,\common\models\Membership::PKEY_AGENCY_HJ_B,\common\models\Membership::PKEY_AGENCY_HE,\common\models\Membership::PKEY_AGENCY_HP]])
                      ->andWhere('dtb_customer_membership.start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('dtb_customer_membership.expire_date > '.new \yii\db\Expression('NOW()'));

            } else if (1 == $model->is_agency) {

               $query2 = clone $query;

               $query2->andWhere(['in', 'dtb_customer_membership.membership_id', [\common\models\Membership::PKEY_AGENCY_HJ_A,\common\models\Membership::PKEY_AGENCY_HJ_B,\common\models\Membership::PKEY_AGENCY_HE,\common\models\Membership::PKEY_AGENCY_HP]])
                      ->andWhere('dtb_customer_membership.start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('dtb_customer_membership.expire_date > '.new \yii\db\Expression('NOW()'))->distinct();

                $query->andFilterWhere(['not in', \common\models\Customer::tableName().'.customer_id', \yii\helpers\ArrayHelper::getColumn($query2->asArray()->all(), 'customer_id')]);
            }
        }

        if(isset($model->agencies)) {
                $three_agency_query = \common\models\CustomerMembership::find()
                      ->where(['BETWEEN',  'membership_id', \common\models\Membership::PKEY_AGENCY_HE, \common\models\Membership::PKEY_AGENCY_HP])
                      ->andWhere('start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('expire_date > '.new \yii\db\Expression('NOW()'))
                      ->groupBy('customer_id')
                      ->having('COUNT(*) >= 3')
                      ->select('customer_id')
                      ->asArray()->all();
                $three_agency = \yii\helpers\ArrayHelper::getColumn($three_agency_query, 'customer_id');

            switch($model->agencies) {
            // なし
            case 99:
                break;
            // HJ
            case 0:

                $query2 = \common\models\CustomerMembership::find()
                      ->where(['BETWEEN',  'dtb_customer_membership.membership_id', \common\models\Membership::PKEY_AGENCY_HJ_A, \common\models\Membership::PKEY_AGENCY_HJ_B])
                      ->andWhere('dtb_customer_membership.start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('dtb_customer_membership.expire_date > '.new \yii\db\Expression('NOW()'))
                      ->groupBy('dtb_customer_membership.customer_id')
                      ->having('COUNT(*) >= 1')
                      ->select('customer_id');

// HJ HEを除外
                $hj_he_query = \common\models\CustomerMembership::find()
                      ->where(['BETWEEN',  'dtb_customer_membership.membership_id', \common\models\Membership::PKEY_AGENCY_HE, \common\models\Membership::PKEY_AGENCY_HJ_B])
                      ->andWhere('dtb_customer_membership.start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('dtb_customer_membership.expire_date > '.new \yii\db\Expression('NOW()'))
                      ->groupBy('dtb_customer_membership.customer_id')
                      ->having('COUNT(*) >= 2')
                      ->select('customer_id');

// HJ HPを除外
                $hj_hp_query = \common\models\CustomerMembership::find()
                      ->where(['BETWEEN',  'dtb_customer_membership.membership_id', \common\models\Membership::PKEY_AGENCY_HJ_A, \common\models\Membership::PKEY_AGENCY_HP])
                      ->andWhere('dtb_customer_membership.start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('dtb_customer_membership.expire_date > '.new \yii\db\Expression('NOW()'))
                      ->groupBy('dtb_customer_membership.customer_id')
                      ->having('COUNT(*) >= 2')
                      ->select('customer_id');


                $query->andFilterWhere(['in', Customer::tableName().'.customer_id', \yii\helpers\ArrayHelper::getColumn($query2->asArray()->all(), 'customer_id')])
                      ->andWhere(['not in', Customer::tableName().'.customer_id', \yii\helpers\ArrayHelper::getColumn($hj_he_query->asArray()->all(), 'customer_id')])
                      ->andWhere(['not in', Customer::tableName().'.customer_id', \yii\helpers\ArrayHelper::getColumn($hj_hp_query->asArray()->all(), 'customer_id')])
                      ->andWhere(['not in', Customer::tableName().'.customer_id', $three_agency, 'customer_id']);

                break;


            // HE
            case 1:

                $query2 = \common\models\CustomerMembership::find()
                      ->where(['dtb_customer_membership.membership_id' => \common\models\Membership::PKEY_AGENCY_HE])
                      ->andWhere('dtb_customer_membership.start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('dtb_customer_membership.expire_date > '.new \yii\db\Expression('NOW()'))
                      ->groupBy('dtb_customer_membership.customer_id')
                      ->having('COUNT(*) >= 1')
                      ->select('customer_id');

// HJ HEを除外
                $hj_he_query = \common\models\CustomerMembership::find()
                      ->where(['BETWEEN',  'dtb_customer_membership.membership_id', \common\models\Membership::PKEY_AGENCY_HE, \common\models\Membership::PKEY_AGENCY_HJ_B])
                      ->andWhere('dtb_customer_membership.start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('dtb_customer_membership.expire_date > '.new \yii\db\Expression('NOW()'))
                      ->groupBy('dtb_customer_membership.customer_id')
                      ->having('COUNT(*) >= 2')
                      ->select('customer_id');

// HE HPを除外
                $he_hp_query = \common\models\CustomerMembership::find()
                      ->where(['IN',  'dtb_customer_membership.membership_id', [\common\models\Membership::PKEY_AGENCY_HE, \common\models\Membership::PKEY_AGENCY_HP]])
                      ->andWhere('dtb_customer_membership.start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('dtb_customer_membership.expire_date > '.new \yii\db\Expression('NOW()'))
                      ->groupBy('dtb_customer_membership.customer_id')
                      ->having('COUNT(*) >= 2')
                      ->select('customer_id');


                $query->andFilterWhere(['in', Customer::tableName().'.customer_id', \yii\helpers\ArrayHelper::getColumn($query2->asArray()->all(), 'customer_id')])
                      ->andWhere(['not in', Customer::tableName().'.customer_id', \yii\helpers\ArrayHelper::getColumn($hj_he_query->asArray()->all(), 'customer_id')])
                      ->andWhere(['not in', Customer::tableName().'.customer_id', \yii\helpers\ArrayHelper::getColumn($he_hp_query->asArray()->all(), 'customer_id')])
                      ->andWhere(['not in', Customer::tableName().'.customer_id', $three_agency, 'customer_id']);



                break;
            // HP
            case 2:

                $query2 = \common\models\CustomerMembership::find()
                      ->where(['dtb_customer_membership.membership_id' => \common\models\Membership::PKEY_AGENCY_HP])
                      ->andWhere('dtb_customer_membership.start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('dtb_customer_membership.expire_date > '.new \yii\db\Expression('NOW()'))
                      ->groupBy('dtb_customer_membership.customer_id')
                      ->having('COUNT(*) >= 1')
                      ->select('customer_id');

// HJ HPを除外
                $hj_hp_query = \common\models\CustomerMembership::find()
                      ->where(['BETWEEN',  'dtb_customer_membership.membership_id', \common\models\Membership::PKEY_AGENCY_HJ_A, \common\models\Membership::PKEY_AGENCY_HP])
                      ->andWhere('dtb_customer_membership.start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('dtb_customer_membership.expire_date > '.new \yii\db\Expression('NOW()'))
                      ->groupBy('dtb_customer_membership.customer_id')
                      ->having('COUNT(*) >= 2')
                      ->select('customer_id');

// HE HPを除外
                $he_hp_query = \common\models\CustomerMembership::find()
                      ->where(['IN',  'dtb_customer_membership.membership_id', [\common\models\Membership::PKEY_AGENCY_HE, \common\models\Membership::PKEY_AGENCY_HP]])
                      ->andWhere('dtb_customer_membership.start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('dtb_customer_membership.expire_date > '.new \yii\db\Expression('NOW()'))
                      ->groupBy('dtb_customer_membership.customer_id')
                      ->having('COUNT(*) >= 2')
                      ->select('customer_id');


                $query->andFilterWhere(['in', Customer::tableName().'.customer_id', \yii\helpers\ArrayHelper::getColumn($query2->asArray()->all(), 'customer_id')])
                      ->andWhere(['not in', Customer::tableName().'.customer_id', \yii\helpers\ArrayHelper::getColumn($hj_hp_query->asArray()->all(), 'customer_id')])
                      ->andWhere(['not in', Customer::tableName().'.customer_id', \yii\helpers\ArrayHelper::getColumn($he_hp_query->asArray()->all(), 'customer_id')])
                      ->andWhere(['not in', Customer::tableName().'.customer_id', $three_agency, 'customer_id']);




                break;
            // HJ HE
            case 3:

                $query2 = \common\models\CustomerMembership::find()
                      ->where(['BETWEEN',  'dtb_customer_membership.membership_id', \common\models\Membership::PKEY_AGENCY_HE, \common\models\Membership::PKEY_AGENCY_HJ_B])
                      ->andWhere('dtb_customer_membership.start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('dtb_customer_membership.expire_date > '.new \yii\db\Expression('NOW()'))
                      ->groupBy('dtb_customer_membership.customer_id')
                      ->having('COUNT(*) >= 2')
                      ->select('customer_id');
                $query->andFilterWhere(['in', Customer::tableName().'.customer_id', \yii\helpers\ArrayHelper::getColumn($query2->asArray()->all(), 'customer_id')])
                      ->andWhere(['not in', Customer::tableName().'.customer_id', $three_agency, 'customer_id']);

                break;
            // HJ HP
            case 4:

                $query2 = \common\models\CustomerMembership::find()
                      ->where(['BETWEEN',  'dtb_customer_membership.membership_id', \common\models\Membership::PKEY_AGENCY_HJ_A, \common\models\Membership::PKEY_AGENCY_HP])
                      ->andWhere('dtb_customer_membership.start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('dtb_customer_membership.expire_date > '.new \yii\db\Expression('NOW()'))
                      ->groupBy('dtb_customer_membership.customer_id')
                      ->having('COUNT(*) >= 2')
                      ->select('customer_id');
                $query->andFilterWhere(['in', Customer::tableName().'.customer_id', \yii\helpers\ArrayHelper::getColumn($query2->asArray()->all(), 'customer_id')])
                      ->andWhere(['not in', Customer::tableName().'.customer_id', $three_agency, 'customer_id']);

                break;

            // HE HP
            case 5:

                $query2 = \common\models\CustomerMembership::find()
                      ->where(['IN',  'dtb_customer_membership.membership_id', [\common\models\Membership::PKEY_AGENCY_HE, \common\models\Membership::PKEY_AGENCY_HP]])
                      ->andWhere('dtb_customer_membership.start_date < '. new \yii\db\Expression('NOW()'))
                      ->andWhere('dtb_customer_membership.expire_date > '.new \yii\db\Expression('NOW()'))
                      ->groupBy('dtb_customer_membership.customer_id')
                      ->having('COUNT(*) >= 2')
                      ->select('customer_id');
                $query->andFilterWhere(['in', Customer::tableName().'.customer_id', \yii\helpers\ArrayHelper::getColumn($query2->asArray()->all(), 'customer_id')])
                      ->andWhere(['not in', Customer::tableName().'.customer_id', $three_agency, 'customer_id']);

                break;
            // HJ HE HP
            case 6:
                $query->andFilterWhere(['in', Customer::tableName().'.customer_id', $three_agency, 'customer_id']);
                break;

            }
        }

        $query->distinct();

        return new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort'  => [
                'attributes' => [
                    'customer_id',
                    'grade_id',
                    'pref_id',
                    'create_date',
                    'name' => [
                        'asc' => ['name01'=>SORT_ASC, 'name02'=>SORT_ASC ],
                        'desc'=> ['name01'=>SORT_DESC,'name02'=>SORT_DESC],
                    ],
                    'kana' => [
                        'asc' => ['kana01'=>SORT_ASC, 'kana02'=>SORT_ASC ],
                        'desc'=> ['kana01'=>SORT_DESC,'kana02'=>SORT_DESC],
                    ],
                ],
                'defaultOrder' => ['customer_id' => SORT_DESC],
            ],
        ]);
    }

    protected function findMigrationModel($from,$id)
    {
        if(! in_array($from, ['webdb20','webdb18']))
            throw new \yii\web\NotFoundHttpException(sprintf("%sからの移行は未対応です",$from));

        if('webdb20' == $from)
            $src = \common\models\webdb20\SearchCustomer::findOne($id);
        if('webdb18' == $from)
            $src = \common\models\webdb18\SearchCustomer::findOne($id);
        if(! isset($src))
            throw new \yii\web\NotFoundHttpException("当該の顧客IDは見付かりません");

        return $src;
    }

    /* @return void */
    private function addInfo($customer_id, $content)
    {
        $info = new \backend\models\CustomerInfo([
            'customer_id'=> $customer_id,
            'created_by' => 0, // system@toyouke.com
            'updated_by' => 0, // system@toyouke.com
            'content'    => $content,
        ]);

        $info->save();
    }

}
