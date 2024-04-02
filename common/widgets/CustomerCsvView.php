<?php
namespace common\widgets;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/CsvView.php $
 * $Id: CsvView.php 2734 2016-07-17 04:07:12Z mori $
 */

use Yii;
use Closure;

class CustomerCsvView extends \yii\base\Widget
{
    /* @var string */
    public  $charset = 'SJIS-WIN';

    /* @var string End Of Line */
    public  $eol = "\r\n";

    /* @var ActiveQuery query */
    public  $query;

    public  $header = [];

    /* @var array of string */
    public  $attributes;

    public function init()
    {
        parent::init();

        if(! $this->header)
            foreach($this->attributes as $key =>$attr)
                $this->header[] = is_string($attr) ? $attr :$key;
    }

    public function run()
    {
        $this->renderHeader();
        $this->renderItems();
    }

    private function renderHeader()
    {
        $this->output(implode(',', $this->header));
    }

    private function renderItems()
    {
        foreach($this->query->asArray()->all() as $rows) {
            $this->renderItem($rows);
            flush();
            ob_flush();
        }
    }

    private function renderItem($model)
    {
        // ランク
        $grade_name = \common\models\CustomerGrade::find()->where(['grade_id' => $model['grade_id']])->asArray()->one();
        // 親会員有無
        $parent = \common\models\CustomerFamily::find()->where(['child_id' => $model['customer_id']])->one();
        $has_parent = ($parent) ? 'yes' : 'no';
        // 家族会員共通情報（住所等）
        $pref = \common\models\Pref::findOne($model['pref_id']);
        $pref_name = ($pref) ? $pref->name : "";
        $zip = ($parent) ? $parent->parent->zip : "{$model['zip01']}-{$model['zip02']}";
        $addr = ($parent) ? $parent->parent->addr : $pref_name . $model['addr01'] . $model['addr02'];
        $tel = ($parent) ? $parent->parent->tel : "{$model['tel01']}-{$model['tel02']}-{$model['tel03']}";
        // メルマガ
        $subscription = \common\models\Subscribe::findOne($model['subscribe']);        
        // 子会員有無
        $children = \common\models\CustomerFamily::find()->where(['parent_id' => $model['customer_id']])->asArray()->all();
        $has_children = ($children) ? 'yes' : 'no';
        // 会員番号
        $mcode = \common\models\Membercode::find()->where(['customer_id' => $model['customer_id'], 'status' => 0])->asArray()->one();
        $customer_code = '';
        if ($parent) {
            $customer_code = $parent->parent->code;
        } else if ($mcode) {
            $customer_code = $mcode['code'];
        }
        // webdb番号
        $w20 = \common\models\Membercode::find()
                                    ->where(['customer_id' => $model['customer_id'],
                                              'directive'   => 'webdb20'])
                                    ->select('migrate_id')
                                    ->scalar();
        $row = [
            $model['customer_id'],
            $grade_name['name'],
            $zip,
            $addr,
            "{$model['name01']} {$model['name02']}",
            "{$model['kana01']} {$model['kana02']}",
            $tel,
            $model['email'],
            $subscription ? $subscription->name : "",
            $customer_code,
            $has_parent,
            $has_children,
            $w20
        ];
        $this->output(implode(',', $row));
    }

    private function output($line)
    {
        if(Yii::$app->charset !== $this->charset)
            $line = mb_convert_encoding($line, $this->charset, Yii::$app->charset);

        echo $line, $this->eol;
    }
}
