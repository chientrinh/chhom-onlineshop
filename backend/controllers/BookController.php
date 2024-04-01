<?php

namespace backend\controllers;

use Yii;
use common\models\Book;
use common\models\SearchBook;
use yii\web\NotFoundHttpException;

/**
 * BookController implements the CRUD actions for Book model.
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/BookController.php $
 * $Id: BookController.php 2736 2016-07-17 06:19:13Z mori $
 */
class BookController extends BaseController
{

    /**
     * @return bool
     */
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['url'=>['/product'],'label'=>'商品'];
        $this->view->params['breadcrumbs'][] = ['url'=>['index'],   'label'=>'書誌'];

        return true;
    }

    /**
     * Lists all Book models.
     * @return mixed
     */
    public function actionIndex($format='html')
    {
        $model    = new SearchBook();
        $provider = $model->search(Yii::$app->request->queryParams);

        if('csv' != $format)
            return $this->render('index', [
                'searchModel'  => $model,
                'dataProvider' => $provider,
            ]);

        // 'csv' == $format
        \common\widgets\CsvView::widget(['query'      => $provider->query,
                                         'attributes' => $model->attributes(),
                                         'charset'    => Yii::$app->charset,
                                         'eol'        => "<br>\n",
        ]);

        return;
    }

    /**
     * Displays a single Book model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Book model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new Book(['product_id'=>$id]);

        if($model->load(Yii::$app->request->post()) && $model->save())
            return $this->redirect(['view', 'id' => $model->product_id]);

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Book model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if($model->load(Yii::$app->request->post()) && $model->save())
            return $this->redirect(['view', 'id' => $model->product_id]);

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the Book model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Book the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = Book::find()->where(['product_id'=>$id])->one();

        if(! $model)
            throw new NotFoundHttpException('The requested page does not exist.');

        return $model;
    }
}
