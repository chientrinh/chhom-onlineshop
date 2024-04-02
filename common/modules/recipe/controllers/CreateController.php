<?php

namespace common\modules\recipe\controllers;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/recipe/controllers/CreateController.php $
 * $Id: CreateController.php 4052 2018-11-07 01:12:48Z kawai $
 */
use Yii;
use yii\helpers\Html;
use \common\models\SearchMember;
use \common\models\Customer;
use \common\models\Product;
use \common\models\Recipe;

class CreateController extends BaseController
{
    protected $enableClientChange = true;

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'] = [];
        $this->layout = 'bootstrap';

        $user = Yii::$app->user->identity;
        if($user instanceof Customer)
            if($user->isStudent() && ! $user->isHomoeopath())
                $this->enableClientChange = false;

        return true;
    }

    public function actionIndex()
    {
        $this->module->recipeForm->load(Yii::$app->request->queryParams);
        $this->module->recipeForm->validate();
        return $this->render('index', [
            'title' => sprintf(($this->module->recipeForm->recipe_id ? "編集" : "新規作成")." | 適用書 | %s", Yii::$app->name),
            'model' => $this->module->recipeForm,
            'client'=> $this->module->client,
            'enableClientChange' => $this->enableClientChange,
        ]);
    }

    /**
     * @brief move position of recipeItem to offset steps
     */
    public function actionMoveItem($seq, $offset)
    {
        $recipe = $this->module->recipeForm;

        if(! isset($recipe->items[$seq]))
            throw new \yii\base\UserException('invalid param: seq');

        if(! is_numeric($offset) || ! isset($recipe->items[$seq + $offset]))
            throw new \yii\base\UserException('invalid param: offset');

        $target = $recipe->items[$seq];
        $swap   = $recipe->items[$seq + $offset];

        $recipe->items[$seq]           = $swap;
        $recipe->items[$seq + $offset] = $target;

        $recipe->items = array_values($recipe->items);

        return $this->redirect('index');
    }

    public function actionFinish()
    {
        $recipe = $this->module->recipeForm;

        if ($recipe->isNewRecord && \yii\helpers\Url::previous('recipe')) {
            $url = (Yii::$app->id === 'app-backend') ? '/backyard/index.php/sodan/interview/' : '/index.php/sodan/interview/view?id=';
            $recipe->itv_id = str_replace($url, '', \yii\helpers\Url::previous('recipe'));
        }

        if(! $recipe->validate())
            return $this->redirect('index');

        if ($recipe->client_id === null && $recipe->recipe_id === null) {

            if (empty($recipe->manual_client_name)) {
                Yii::$app->session->addFlash('danger', 'クライアントを入力して下さい');
                return $this->redirect('index');
            }
            $recipe->status = Recipe::STATUS_PREINIT;
        }

        if ($this->module->insertRecord())
        {
            $route = $this->module->defaultRoute;

            if($itv_id = $recipe->itv_id) {
                // 相談会のステータスを「適用書作成済」にする
                if ($itv_model = \common\models\sodan\Interview::findOne($itv_id)) {
                    $itv_model->status_id = \common\models\sodan\InterviewStatus::PKEY_DONE;
                    $itv_model->save();
                }
                return $this->redirect(["/sodan/interview/view", 'id' => $itv_id]);
            }
            return $this->redirect(["/recipe/$route/view",'id'=>$recipe->recipe_id]);
        }

        return $this->redirect('index');
    }

    public function actionCompose()
    {
        $model = new \common\components\cart\ComplexRemedyForm([
            'scenario'     => 'prescribe',
            'maxDropLimit' => 5,
        ]);

        if($hpath = $this->module->recipeForm->homoeopath)
            if($hpath->name === "由井 寅子")
                $model->maxDropLimit = 100;

        if(Yii::$app->request->get())
        {
            $model->load(Yii::$app->request->get());
            $model->validate();
        }

        if('extend' == Yii::$app->request->get('command', null))
            $model->extend();

        if('shrink' == Yii::$app->request->get('command', null))
            $model->shrink();

        if(('finish' == Yii::$app->request->get('command', null)) && ! $model->hasErrors())
            if($model->validate() && $this->module->recipeForm->addItem($model))
            {
                Yii::$app->session->addFlash('success', sprintf('%s が追加されました',$model->name));
                return $this->redirect('compose'); // trim off get params, renew the screen
            }

        return $this->render('compose', ['model'=>$model,'recipe'=>$this->module->recipeForm]);
    }

    public function actionMachine()
    {
        $model = new \common\models\MachineRemedyForm();

        if($model->load(Yii::$app->request->post()) &&
           $model->validate() &&
           $this->module->recipeForm->addItem($model))
        {
            Yii::$app->session->addFlash('success', sprintf('%s が追加されました',$model->name));

            return $this->redirect('machine');
        }

        return $this->render('machine', ['model'=>$model,'recipe'=>$this->module->recipeForm]);
    }

    public function actionAdd($target)
    {
        if('client' == $target)
            return $this->actionAddClient(Yii::$app->request->getQueryParam('code'));

        if('homoeopath' == $target)
            return $this->actionAddHomoepath(Yii::$app->request->post('id'));

        if('product' == $target)
            return $this->actionAddProduct(Yii::$app->request->getQueryParam('id'));

        if(in_array($target, ['remedy', 'tincture', 'flower', 'flower2', 'nonpublic', 'jm'] ))
            return $this->actionAddRemedy(Yii::$app->request->getQueryParam('rid'),
                                          Yii::$app->request->getQueryParam('pid'),
                                          Yii::$app->request->getQueryParam('vid'),
                                          Yii::$app->request->getQueryParam('stock'),
                                          $target);
        if('qty'== $target)
            return $this->actionAddQty(Yii::$app->request->getQueryParam('seq'),
                                       Yii::$app->request->getQueryParam('vol'));

        if('instruction' == $target)
            return $this->addInstruction(Yii::$app->request->getQueryParam('index'),
                                         Yii::$app->request->getQueryParam('value'));

        if('note' == $target)
            return $this->addNote(Yii::$app->request->getQueryParam('note'));

        if('memo' == $target)
            return $this->addMemo(Yii::$app->request->getQueryParam('memo'));

        if (in_array($target, ['manual_client_name', 'manual_client_age', 'manual_protector_name', 'manual_protector_age', 'center', 'tel']))
            return $this->addData($target, Yii::$app->request->getQueryParam($target));


        throw new \yii\web\NotFoundHttpException("ご指定のURLは見つかりません");
    }

    public function actionApply()
    {
        $params = Yii::$app->request->post();
        $this->applyParams($params);

    }

    public function applyParams($params)
    {
        if($params) {
            $action = "";

            foreach($params as $key => $value)
            {
                if (in_array($key, ['manual_client_name', 'manual_client_age', 'manual_protector_name', 'manual_protector_age', 'center', 'tel']))
                {
                    if(!in_array($key, ['center', 'tel']) && $this->module->recipeForm->client_id)
                    {
                        $this->module->recipeForm->manual_client_name = null;
                        $this->module->recipeForm->manual_client_age = null;
                        $this->module->recipeForm->manual_protector_name = null;
                        $this->module->recipeForm->manual_protector_age = null;

                        continue;
                    }
                    $this->addData($key, $value);
                    continue;
                }

                if($key == 'quantity' || $key == 'memo' || $key == 'instruct_id') {
                    $this->setParamsToItems($key, $value);
                    continue;
                }

                if($key == 'note') {
                    $this->addNote($value);
                    continue;
                }
                if($key == 'action') {
                    $action = $value;
                }
            }

            if(isset($action)){
                if($action == 'search-client-delete')
                    return $this->redirect(['del', 'target' => 'search-client-delete'], 200);

                if($action == 'search-client')
                    return $this->redirect(['search', 'target' => 'client'], 200);

                if($action == 'move-item') {
                    $offsets = explode("_", $params['target']);
                    return $this->redirect([$action,'seq' => $offsets[0], 'offset' => $offsets[1]], 200);
                }

                if($action == 'del-item') {
                    $seq = explode("_", $params['target']);
//                    item_seq_'+index
                    unset($this->module->recipeForm->items[$seq[2]]);

                    return $this->redirect(['index'], 200);
                }




                return $this->redirect([$action], 200);
            }
        } else {
            Yii::$app->session->addFlash('error',
                                         sprintf('不正なリクエストが実行されました')
            );
        }
        return $this->redirect(['index'], 200);
    }

    private function setParamsToItems($key, $value)
    {
        $array = explode('&', $value);
        array_splice($array, 0, 1);
        $items = $this->module->recipeForm->items;
        foreach($array as $k => $val) {
            if(isset($items[$k])) {
                if('quantity' == $key && $val <= 0){
                    unset($this->module->recipeForm->items[$k]);
                    unset($array[$k]);
                    continue;
                }

                $this->module->recipeForm->items[$k]->$key = $val;
                continue;
            }
        }

    }

    public function actionDel($target)
    {
        if('item' == $target)
            return $this->actionDelItem(Yii::$app->request->getQueryParam('seq'));

        if ('search-client-delete' == $target)
            return $this->actionDelClient();

        if('all' == $target)
        {
            $this->module->recipeForm->items = [];
            $this->module->recipeForm->client_id = null;
            $this->module->recipeForm->note = null;
            $this->module->recipeForm->manual_client_name = null;
            $this->module->recipeForm->manual_client_age = null;
            $this->module->recipeForm->manual_protector_name = null;
            $this->module->recipeForm->manual_protector_age = null;
            $this->module->recipeForm->center = null;
            $this->module->recipeForm->tel = null;
            $this->module->client = new Customer();
            return $this->redirect('index');
        }

        throw new \yii\web\NotFoundHttpException("ご指定のURLは見つかりません");
    }

    private function actionDelItem($seq)
    {
        unset($this->module->recipeForm->items[$seq]);

        return $this->redirect('index');
    }

    private function actionDelClient()
    {
        $this->module->recipeForm->client_id = null;

        return $this->redirect('index');
    }

    public function actionMigrateCustomer($code)
    {
        if(! $this->enableClientChange)
            throw new \yii\web\ForbiddenHttpException("学生はこの操作を実行できません");

        if($this->module->setClient($this->findClient($code)))
            Yii::$app->session->addFlash('success',"$code の移行手続きが完了しました");
        else
            Yii::$app->session->addFlash('error',"$code の移行手続きが完了しませんでした");

        return $this->redirect(['index']);
    }

    public function actionSearch($target, $startwith = null)
    {
        if('client' == $target)
            return $this->actionSearchClient();

        if('product' == $target)
            return $this->actionSearchProduct();

        if('tincture' == $target)
            return $this->actionSearchTincture($startwith);

        if('flower' == $target)
            return $this->actionSearchFlower($startwith);

        if('flower2' == $target)
            return $this->actionSearchFlower2($startwith);

        if('nonpublic' == $target)
            return $this->actionSearchNonPublic($startwith);

        if ('jm' === $target)
            return $this->actionSearchJm();


        return $this->actionSearchRemedy($startwith);
    }

    private function actionAddClient($code)
    {
        if($this->module->setClient($this->findClient($code)))
            return $this->redirect('index');

        return $this->redirect(['search','target'=>'client']);
    }

    private function actionAddHomoepath($id)
    {
        if(! Yii::$app->user->identity instanceof \backend\models\Staff)
            throw new \yii\web\ForbiddenHttpException('You cannot change homoeopath_id of a recipe');

        $this->module->recipeForm->homoeopath_id = $id;
        return $this->redirect('index');
    }

    private function addInstruction($index, $value)
    {
        if(isset($this->module->recipeForm->items[$index]))
            $this->module->recipeForm->items[$index]->instruct_id = $value;

        return $this->redirect(['index']);
    }

    private function addMemo($params)
    {
        $items = $this->module->recipeForm->items;

        foreach($params as $k => $value)
            if(isset($items[$k]))
                $this->module->recipeForm->items[$k]->memo = $value;

//        return $this->redirect(['index']);
    }

    private function addNote($note)
    {
        $this->module->recipeForm->note = $note;

//        return $this->redirect(['index']);
    }

    private function addData($columnName, $data)
    {
        $this->module->recipeForm->$columnName = $data;

//        return $this->redirect(['index']);
    }

    private function actionAddQty($seq,$vol)
    {
        $seq = (int)$seq;
        $vol = (int)$vol;

        if(isset($this->module->recipeForm->items[$seq]))
            $this->module->recipeForm->items[$seq]->quantity += $vol;

        if($this->module->recipeForm->items[$seq]->quantity <= 0)
            unset($this->module->recipeForm->items[$seq]);

        return $this->redirect('index');
    }

    private function actionAddProduct($id)
    {
        if($this->module->recipeForm->addItem($this->findProduct($id)))
            Yii::$app->session->addFlash('success',
                                         sprintf('%s が追加されました',end($this->module->recipeForm->items)->name)
            );

        return $this->redirect(['search','target'=>'product']);
    }

    private function actionAddRemedy($rid,$pid,$vid,$stock, $target = 'remedy')
    {
        $ok = $this->module->recipeForm->addItem($this->findRemedy($rid,$pid,$vid,$stock));
        if(Yii::$app->request->isAjax && $ok)
            return 'ok';

        if($ok) {
            Yii::$app->session->addFlash('success',
                                         sprintf('%s が追加されました',end($this->module->recipeForm->items)->name)
            );
        } else {
            Yii::$app->session->addFlash('danger',
                                         sprintf('%s の追加に失敗しました',end($this->module->recipeForm->items)->name)
            );
        }

        return $this->redirect(['search','target'=>$target]);
    }

    private function actionSearchClient()
    {
        $model    = new SearchMember();
        $provider = new \yii\data\ArrayDataProvider(['allModels'=>[]]);
        $tel = null;

        if (Yii::$app->request->get('mcode')) {
            $tel = Yii::$app->request->get('mcode');
        }
        if (Yii::$app->request->post('tel')) {
            $tel = Yii::$app->request->post('tel');
        }

        if($tel) {
            $model->tel = $tel;

            if(true == $model->validate())
                $provider = $this->loadProvider($model->tel);
        }

        return $this->render('search-client',[
            'dataProvider' => $provider,
            'searchModel'  => $model,
        ]);
    }

    private function actionSearchProduct()
    {
        // セットの除外は「セット」を名前に含まない、でも出来るが、サブカテゴリの順位をどう反映させて並べるか？？
        $query = Product::find()
               ->active()
               ->andwhere([Product::tableName().'.category_id' => \common\models\Category::REMEDY])
               ->andWhere(['like', Product::tableName().'.name', 'キット']);


        $dataProvider = new \yii\data\ActiveDataProvider([
            'query'     => $query,
            'pagination'=>false,
            'sort' => false
 //            'sort' => [
 //               'defaultOrder' => ['dsp_priority'=>SORT_DESC],
 // //                'defaultOrder' => ['kana'=>SORT_ASC],
 //            ],
        ]);
        $dataProvider->query->joinWith('productMaster')
                            ->andWhere(['not', ['mvtb_product_master.name' => '']])
                            ->orderBy(['dsp_priority' => SORT_DESC]);


        // フロントエンドならサイト非公開を除外する
        if(Yii::$app->id == 'app-frontend') {
            $dataProvider->query->andWhere(['not', ['mvtb_product_master.restrict_id' => 99]]);
        }

        return $this->render('product',[
            'dataProvider' => $dataProvider,
            'recipe'       => $this->module->recipeForm,
        ]);
    }

    private function actionSearchRemedy($startwith)
    {
        $searchModel  = new \common\models\SearchRemedyStock([
            'remedy'  => $startwith,
            'vials'   => \yii\helpers\ArrayHelper::getColumn(
                \common\models\RemedyVial::find()->remedy()->all(),'vial_id'
            )
        ]);


        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


        $dataProvider->query->tinctureAndFlower(false);
        $dataProvider->query->from('mvtb_product_master');
        $dataProvider->query->andWhere(['not', ['name' => '']]);

        // フロントエンドならサイト非公開を除外する
        if(Yii::$app->id == 'app-frontend') {
            $dataProvider->query->andWhere(['not', ['mvtb_product_master.restrict_id' => 99]]);
        }

        return $this->render('remedy',[
            'target'   => 'all',
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
            'recipe'       => $this->module->recipeForm,
        ]);
    }

    private function actionSearchTincture($startwith)
    {
        $searchModel  = new \common\models\SearchRemedyStock([
            'remedy'     => $startwith,
            'potencies' => [\common\models\RemedyPotency::MT],
            'vials'   => \yii\helpers\ArrayHelper::getColumn(
                \common\models\RemedyVial::find()->tincture()->all(),'vial_id'
            )
        ]);


        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->from('mvtb_product_master');
        $dataProvider->query->andWhere(['not', ['name' => '']]);
        $dataProvider->query->andWhere(['not in', 'mtb_remedy.remedy_id', ['1471']]);

        // フロントエンドならサイト非公開を除外する
        if(Yii::$app->id == 'app-frontend') {
            $dataProvider->query->andWhere(['not', ['mvtb_product_master.restrict_id' => 99]]);
        }

        return $this->render('remedy',[
            'target'       => 'tincture',
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
            'recipe'       => $this->module->recipeForm,
        ]);
    }

    /**
     * フラワーエッセンス(potency_idが「FE」)のみを検索して返す
     * @param type $startwith
     * @return type
     */
    private function actionSearchFlower($startwith)
    {
        $potencies = \yii\helpers\ArrayHelper::map(\common\models\RemedyPotency::find()->flower()->all(), 'potency_id', 'name');

        $searchModel  = new \common\models\SearchRemedyStock([
            'potencies' => array_flip($potencies),
       ]);


        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->from('mvtb_product_master');
        $dataProvider->query->andWhere(['not', ['name' => '']]);

        // フロントエンドならサイト非公開を除外する
        if(Yii::$app->id == 'app-frontend') {
            $dataProvider->query->andWhere(['not', ['mvtb_product_master.restrict_id' => 99]]);
        }

        return $this->render('remedy',[
            'target'       => 'flower',
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
            'recipe'       => $this->module->recipeForm,
        ]);
    }

    /**
     * フラワーエッセンス(potency_idが「FE2」)のみを検索して返す
     * @param type $startwith
     * @return type
     */
    private function actionSearchFlower2($startwith)
    {
        $potencies = \yii\helpers\ArrayHelper::map(\common\models\RemedyPotency::find()->flower2()->all(), 'potency_id', 'name');

        $searchModel  = new \common\models\SearchRemedyStock([
            'potencies' => array_flip($potencies),
       ]);


        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->from('mvtb_product_master');
        $dataProvider->query->andWhere(['not', ['name' => '']]);

        // フロントエンドならサイト非公開を除外する
        if(Yii::$app->id == 'app-frontend') {
            $dataProvider->query->andWhere(['not', ['mvtb_product_master.restrict_id' => 99]]);
        }

        return $this->render('remedy',[
            'target'       => 'flower2',
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
            'recipe'       => $this->module->recipeForm,
        ]);
    }

    /**
     * 一般処方不可のレメディー（2017/10/19現在、グレートブレッシングのみ）を検索して返す
     * @param type $startwith
     * @return type
     */
    private function actionSearchNonPublic($startwith)
    {

        $searchModel  = new \common\models\SearchRemedyStock([
            'remedy'  => $startwith,
            'vials'   => \yii\helpers\ArrayHelper::getColumn(
                \common\models\RemedyVial::find()->all(),'vial_id'
            )
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['in', 'mtb_remedy.remedy_id', ['1471']]);
        $dataProvider->query->from('mvtb_product_master');
        $dataProvider->query->andWhere(['not', ['name' => '']]);

        // フロントエンドならサイト非公開を除外する
        if(Yii::$app->id == 'app-frontend') {
            $dataProvider->query->andWhere(['not', ['mvtb_product_master.restrict_id' => 99]]);
        }

        return $this->render('remedy',[
            'target'       => 'nonpublic',
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
            'recipe'       => $this->module->recipeForm,
        ]);
    }

    /**
     * フラワーエッセンス(potency_idが「FE」)のみを検索して返す
     * @return type
     */
    private function actionSearchJm()
    {
        $potencies = \yii\helpers\ArrayHelper::map(\common\models\RemedyPotency::find()->jm()->all(), 'potency_id', 'name');

        $searchModel  = new \common\models\SearchRemedyStock([
            'potencies' => array_flip($potencies),
       ]);


        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->from('mvtb_product_master');
        $dataProvider->query->andWhere(['not', ['name' => '']]);

        return $this->render('remedy',[
            'target'       => 'jm',
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
            'recipe'       => $this->module->recipeForm,
        ]);
    }

    public function actionView()
    {
        if(Yii::$app->request->get()) {
            var_dump(Yii::$app->request->get());$this->actionApply(Yii::$app->request->get());
        }
        $html = \common\widgets\doc\recipe\RecipeDocument::widget([
            'model' => $this->module->recipeForm,
        ]);

        $this->layout = '/none';
        $this->view->title = '適用書プレビュー | ' . Yii::$app->name;

        return $this->renderContent($html);
    }

    public function actionAgreed()
    {
        return $this->render('agreed');
    }

    public function actionCreateCustomer($agreed = 0)
    {        
        if(! $agreed)
            throw new \yii\web\BadRequestHttpException("利用規約に同意の上、この画面へお進みください");

        $model = new Customer(['grade_id' => 1]);

        if(Yii::$app->request->isPost)
        {
            $model->load(Yii::$app->request->post());

            if('zip2addr' === Yii::$app->request->post('scenario'))
                $model->zip2addr();

            elseif($model->save() ||
                   ($model->validate(['name01','name02','kana01','kana02','tel01','tel02','tel03']) && $model->save(false))){

                if (Yii::$app->request->post('target') === 'child') {
                    return $this->redirect(["create-child?parent_id={$model->customer_id}"]);
                }
                return $this->redirect(["attach-membercode?id={$model->customer_id}&mode=recipe"]);
            }
        }

        return $this->render('create-customer', [
            'model'  => $model,
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
                        return $this->redirect(['/recipe/create/search?target=client&mcode=' . $model->membercode->code]);
                    }
                }
            }
        }

        return $this->render('create-child', [
            'model' => $model,
            'parent_id' => $parent_id
        ]);
    }

    public function actionUpdateCustomer($id)
    {
        $model = Customer::findOne($id);

        if(Yii::$app->request->isPost)
        {
            $model->load(Yii::$app->request->post());

            if('zip2addr' === Yii::$app->request->post('scenario'))
                $model->zip2addr();

            elseif($model->save() ||
                   ($model->validate(['name01','name02','kana01','kana02','tel01','tel02','tel03']) && $model->save(false))){

                if (Yii::$app->request->post('target') === 'child') {
                    return $this->redirect(["create-child?parent_id={$model->customer_id}"]);
                }
                return $this->redirect(["attach-membercode?id={$model->customer_id}&mode=recipe"]);
            }
        }

        return $this->render('update-customer', [
            'model'  => $model,
        ]);
    }

    public function actionAttachMembercode($id, $mcode = null, $pw = null)
    {
        $customer = Customer::findOne($id);
        $prev     = $customer->membercode->code;

        if(13 == strlen($mcode)) {
            $substr10 = substr($mcode, 2, 10);
            $mcode    = $substr10;
        }

        if(strlen($mcode) && strlen($pw)) {
            $model = \common\models\Membercode::find()->where(['code'=>$mcode,'pw'=>$pw])->one();

            if( $model && \common\components\CustomerMigration::attachMembercode($customer, $model)) {
                Yii::$app->session->addFlash('success', sprintf('会員証NOを更新しました(%s -> %s)', $prev, $mcode));
                return $this->redirect(['/recipe/create/search?target=client&mcode=' . $prev]);
            }
            Yii::$app->session->addFlash('error',"会員証NO無効、またはPWが一致しません");
        }

        $model = new \common\models\Membercode(['code'=>$mcode, 'pw'=>$pw]);
        return $this->render('attach-membercode',['customer' => $customer, 'model' => $model, 'mode' => Yii::$app->request->get('mode')]);
    }

    protected function findClient($code)
    {
        if(null === ($model = \common\models\Membercode::findOne($code)))
           throw new \yii\web\NotFoundHttpException('当該クライアントIDが見つかりません');

        return $model;
    }

    /* @return Product */
    protected function findProduct($id)
    {
        if(null === ($model = \common\models\Product::findOne($id)))
           throw new \yii\web\NotFoundHttpException('当該クライアントIDが見つかりません');

        if($model->start_date && (time() < strtotime($model->start_date)))
            throw new \yii\web\NotFoundHttpException(sprintf('%s はまだ販売していません', $model->name));

        if($model->expire_date && (strtotime($model->expire_date) < time()))
            throw new \yii\web\NotFoundHttpException(sprintf('%s は販売を終了しました', $model->name));

        return $model;
    }

    /* @return RemedyStock */
    protected function findRemedy($rid,$pid,$vid,$stock)
    {
        $model = \common\models\RemedyStock::find()->where([
            'remedy_id'  => $rid,
            'potency_id' => $pid,
            'vial_id'    => $vid,
        ])->one();
        if(! $model)
            if(!$stock)
                throw new \yii\web\NotFoundHttpException('当該レメディーが見つかりません');
            else
                $model = new \common\models\RemedyStock([
                    'remedy_id'  => $rid,
                    'potency_id' => $pid,
                    'vial_id'    => $vid,
                ]);

        return $model;
    }

    private function loadProvider($key)
    {
        $query = Customer::find()->where(['CONCAT(tel01,tel02,tel03)' => $key]);

        if(! $query->exists())
        {
            if(13 == strlen($key)) // if barcode is given
                $key = substr($key, 2, 10);

            $q2 = \common\models\Membercode::find()->where(['code' => $key]);

            if($q2->exists())
                $query = Customer::find()->where(['customer_id' => $q2->select('customer_id') ]);
        }
        $q3 = clone($query);
        $q4 = \common\models\CustomerFamily::find()->where(['parent_id' => $q3->select('customer_id') ]);

        $query->orFilterWhere(['customer_id' => $q4->select('child_id') ]);

        return new \yii\data\ActiveDataProvider(['query'=> $query]);
    }
}
