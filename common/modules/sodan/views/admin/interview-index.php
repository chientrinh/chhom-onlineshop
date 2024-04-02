<?php 
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/admin/interview-index.php $
 * $Id: interview-index.php 3851 2018-04-24 09:07:27Z mori $
 *
 * $this \yii\web\View
 * $dataProvider
 * $searchModel
 */
use \yii\helpers\Html;

$this->params['breadcrumbs'][] = ['label'=>'相談会','url'=>['interview-index']];

?>

<?= $this->render('interview-grid',[
    'dataProvider' => $dataProvider,
    'searchModel'  => $searchModel,
]) ?>

