<?php

namespace frontend\widgets;

use Yii;
use \common\models\Membership;
use \common\models\CustomerMembership;
use \common\models\sodan\Client;
use \common\models\sodan\Homoeopath;

use \yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

/**
 * @link $URL: https://tarax.toyouke.com/svn/MALL/frontend/widgets/PrivilegeLinks.php $
 * @version $Id: PrivilegeLinks.php 4261 2020-04-28 23:35:34Z mori $
 */
class PrivilegeLinks extends \yii\base\Widget
{
    /* @var \common\models\Customer */
    public $customer;
    public $now_live_items;
    public $next_live_items;
    public $end_live_items;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if(! $this->customer)
            throw new \yii\base\InvalidParamException('customer must be set');

    }

    public function run()
    {
        // echomアプリ用の処理
        if(Yii::$app instanceof \yii\web\Application && ('echom-frontend' == Yii::$app->id)) {
            $this->getLiveItems();

            echo Html::tag('h2', Html::tag('span', 'ライブ配信'));
            // 配信中
            // var_dump($this->now_live_items, $this->next_live_items, $this->end_live_items);exit;
            if(isset($this->now_live_items)) {
                echo '<div class="panel panel-default">',"\n" . \yii\bootstrap\Nav::widget(['items' => $this->now_live_items, 'options'=>['class'=>'panel-body']]) . '</div>',"\n";
            }

            // 配信予定
            if(isset($this->next_live_items)) {
                echo '<div class="panel panel-default">',"\n" . \yii\bootstrap\Nav::widget(['items' => $this->next_live_items, 'options'=>['class'=>'panel-body']]) . '</div>',"\n";
            }

            // 配信終了
            if(isset($this->end_live_items)) {
                echo '<div class="panel panel-default">',"\n" . \yii\bootstrap\Nav::widget(['items' => $this->end_live_items, 'options'=>['class'=>'panel-body']]) . '</div>',"\n";
            }

            return;
        }
    
        foreach($this->customer->memberships as $model)
        {
            $idx   = $model->membership_id;
            $items = $this->getItems($idx);

            if(! $items){ continue; }

            foreach($items as $k => $item)
                $items[$k] = Html::tag('li', $item);

            echo Html::tag('h2',
                           Html::tag('span',
                                     $model->name . Html::tag('small', $model->company->name)
                           )
            );
            echo '<div class="panel panel-default">',"\n";

            echo \yii\bootstrap\Nav::widget(['items' => $items, 'options'=>['class'=>'panel-body']]);

            echo '</div>',"\n";
        }

        $client = Client::find()->where(['client_id' => $this->customer->customer_id])->one();
        if ($client) {
            $items = [
                self::getItem('/recipe/review/index'),
                self::getItem('sodan-upload')
            ];
            foreach($items as $k => $item) {
                $items[$k] = Html::tag('li', $item);
            }
            echo Html::tag('h2', Html::tag('span', '健康相談'));
            echo '<div class="panel panel-default">',"\n" . \yii\bootstrap\Nav::widget(['items' => $items, 'options'=>['class'=>'panel-body']]) . '</div>',"\n";
        }
    }

    private function getLiveItems()
    {
        $now_date = date('Y-m-d H:i:s');
        $now_day = date('Y-m-d');
        $date = strtotime($now_date);

        $query = \common\models\StreamingBuy::find()->select(['dtb_streaming.streaming_id','customer_id','dtb_streaming_buy.create_date'])->innerJoinWith('streaming')->where(['customer_id' => $this->customer->customer_id])->andWhere('DATE(`dtb_streaming`.`expire_from`) <= \''.$now_day.'\'')->andWhere('DATE(`dtb_streaming`.`expire_to`) >= \''.$now_day.'\'');
        $next_query = \common\models\StreamingBuy::find()->select(['dtb_streaming.streaming_id','customer_id','dtb_streaming_buy.create_date'])->innerJoinWith('streaming')->where(['customer_id' => $this->customer->customer_id])->andWhere('DATE(`dtb_streaming`.`expire_from`) > \''.$now_day.'\'')->orderBy(['expire_from' => SORT_ASC]);
        $end_query = \common\models\StreamingBuy::find()->select(['dtb_streaming.streaming_id','customer_id','dtb_streaming_buy.create_date'])->innerJoinWith('streaming')->where(['customer_id' => $this->customer->customer_id])->andWhere('`dtb_streaming`.`expire_to` <= \''.$now_date.'\'')->andWhere('`dtb_streaming`.`expire_date` > \''.$now_date.'\'')->orderBy(['expire_from' => SORT_DESC]);


        if(!$query->exists() && !$next_query->exists() && !$end_query->exists()) {
            $this->now_live_items[] = '<div style="border: 0.2px solid #F0F0F0; padding:7px; margin:10px;">';
            $this->now_live_items[] = Html::tag('p', '有効なライブ配信チケットがありません');
            $this->now_live_items[] = '</div>';
            return;
        }

        if($query->exists()) {
            $models = $query->distinct()->all();

            $this->now_live_items = [

                Html::tag('h3', Html::tag('span', '本日のライブ配信')),
                '<div id="w2" class="grid-view"><div class="summary">本日のライブ配信は'.count($models).' 件です。</div>',
                $this->now_live_items[] = Html::tag('p')
            ];
            foreach($models as $model) {
                // var_dump($model);
                $streaming = $model->getStreaming()->one();
                $link = Html::a('<span class="btn-label">視聴する</span>', $streaming->streaming_url,['target' => '_blank','class' => 'btn btn-danger']);                   

                if($date >= strtotime($streaming->expire_from) && $date <= strtotime($streaming->expire_to)) {
                } else if($date < strtotime($streaming->expire_from)) {
                    $link = Html::button('配信待ち',
                    ['class'=>'btn btn-primary']);
                } else {
                    $link = Html::button('配信終了',
                    ['class'=>'btn btn-secondary']);// 配列データの場合は $data['name']。例えば、SqlDataProvider を使う場合。
                }                    

                $this->now_live_items[] = '<div style="border: 0.2px solid #F0F0F0; padding:7px; margin:10px;">';
                $this->now_live_items[] = Html::tag('p', '■タイトル');
                $this->now_live_items[] = Html::tag('p', $model->getStreaming()->one()->name);
                $this->now_live_items[] = Html::tag('p', '■視聴可能期間');
                $this->now_live_items[] = Html::tag('p', $streaming->expire_from.' 〜 '.$streaming->expire_to);
                $this->now_live_items[] =  Html::tag('p', $link);
                $enquate = $date >= strtotime($streaming->expire_from) ? Html::a('視聴した感想はこちら',$streaming->post_url,['target' => '_blank']) : '';
                $pdf = $streaming->document_url ? Html::a('配布資料はこちら',$streaming->document_url,['target' => '_blank']) : 'なし';

                $data = '<table class="table table-striped table-bordered">
                <thead>
                <tr><th>アンケート</th><th>配布資料</th></tr>
                </thead>
                <tbody><tr data-key=""><td>'.$enquate.'</td><td>'.$pdf.'</td></tr></tbody></table></div>';
                $this->now_live_items[] = $data;
            }
        }

        if($next_query->exists()) {

            $nextDataProvider = new ActiveDataProvider([
                'query' => $next_query->distinct(),
                'pagination' => [
                    'pageSize' => 20,
                ],
            ]);
            $this->next_live_items = [
                Html::tag('h3', Html::tag('span', '配信予定')),
                GridView::widget([
                'dataProvider' => $nextDataProvider,
                    'columns' => [
                        // モデルのカラムのデータが使われる
                        // 複雑なカラム定義
                        [
                            'label' => 'タイトル',
                            'class' => 'yii\grid\DataColumn', // 省略可。これがデフォルト値。
                            'value' => function ($data) {
                                return $data->getStreaming()->one()->name; // 配列データの場合は $data['name']。例えば、SqlDataProvider を使う場合。
                            },
                        ],
                        [
                            'label' => '配信日',
                            'format'=> 'raw',
                            'class' => 'yii\grid\DataColumn', // 省略可。これがデフォルト値。
                            'value' => function ($data) {
                                $streaming = $data->getStreaming()->one();
                                // print(date('Y-m-d',strtotime($streaming->expire_from)));exit;
                                return date('Y-m-d',strtotime($streaming->expire_from));
                            },
                        ],
                        [
                            'label' => 'アンケート',
                            'format'=> 'raw',
                            'class' => 'yii\grid\DataColumn', // 省略可。これがデフォルト値。
                            'value' => function ($data) use ($date){
                                $streaming = $data->getStreaming()->one();
                                if($date >= strtotime($streaming->expire_from)) {
                                    return Html::a('視聴した感想はこちら',$streaming->post_url,['target' => '_blank']);
                                } else {
                                    return ''; // 配列データの場合は $data['name']。例えば、SqlDataProvider を使う場合。
                                }
                            },
                        ],
                        [
                            'label' => '配布資料',
                            'format'=> 'raw',
                            'class' => 'yii\grid\DataColumn', // 省略可。これがデフォルト値。
                            'value' => function ($data) use ($date){
                                $streaming = $data->getStreaming()->one();
                                if($streaming->document_url) {
                                    return Html::a('配布資料はこちら',$streaming->document_url,['target' => '_blank']);
                                } else {
                                    return 'なし';
                                }
                            },
                        ],
                    ],
                ])
            ];
        }


        if($end_query->exists()) {

            $endDataProvider = new ActiveDataProvider([
                'query' => $end_query->distinct(),
                'pagination' => [
                    'pageSize' => 20,
                ],
            ]);
            $this->end_live_items = [
                Html::tag('h3', Html::tag('span', '配信終了')),
                GridView::widget([
                'dataProvider' => $endDataProvider,
                    'columns' => [
                        // モデルのカラムのデータが使われる
                        // 複雑なカラム定義
                        [
                            'label' => 'タイトル',
                            'class' => 'yii\grid\DataColumn', // 省略可。これがデフォルト値。
                            'value' => function ($data) {
                                return $data->getStreaming()->one()->name; // 配列データの場合は $data['name']。例えば、SqlDataProvider を使う場合。
                            },
                        ],
                        [
                            'label' => '配信日',
                            'format'=> 'raw',
                            'class' => 'yii\grid\DataColumn', // 省略可。これがデフォルト値。
                            'value' => function ($data) {
                                $streaming = $data->getStreaming()->one();
                                return  date('Y-m-d', strtotime($streaming->expire_from)); // 配列データの場合は $data['name']。例えば、SqlDataProvider を使う場合。
                            },
                        ],
                        [
                            'label' => 'アンケート',
                            'format'=> 'raw',
                            'class' => 'yii\grid\DataColumn', // 省略可。これがデフォルト値。
                            'value' => function ($data) use ($date){
                                $streaming = $data->getStreaming()->one();
                                if($date >= strtotime($streaming->expire_from)) {
                                    return Html::a('視聴した感想はこちら',$streaming->post_url,['target' => '_blank']);
                                } else {
                                    return ''; // 配列データの場合は $data['name']。例えば、SqlDataProvider を使う場合。
                                }
                            },
                        ],
                        [
                            'label' => '配布資料',
                            'format'=> 'raw',
                            'class' => 'yii\grid\DataColumn', // 省略可。これがデフォルト値。
                            'value' => function ($data) use ($date){
                                $streaming = $data->getStreaming()->one();
                                if($streaming->document_url) {
                                    return Html::a('配布資料はこちら',$streaming->document_url,['target' => '_blank']);
                                } else {
                                    return 'なし';
                                }
                            },
                        ],
                    ],
                ])
            ];
        }
    }

    private function getItems($mship)
    {
        // 開業前はすべて非公開とする
        if('production' == YII_ENV)
            return [ Html::tag('li', "ただいま準備中です") ];

//        if(in_array($mship,[Membership::PKEY_TORANOKO_GENERIC,
//                            Membership::PKEY_TORANOKO_GENERIC_UK])
//        )
//            return [
//                self::renderExpireDate([Membership::PKEY_TORANOKO_GENERIC,
//                                        Membership::PKEY_TORANOKO_GENERIC_UK]),
//                self::getItem('/recipe/review/search'),
//            ];

//        if(in_array($mship,[Membership::PKEY_TORANOKO_NETWORK,
//                            Membership::PKEY_TORANOKO_NETWORK_UK])
//        )
//            return [
//                self::renderExpireDate([Membership::PKEY_TORANOKO_NETWORK,
//                                        Membership::PKEY_TORANOKO_NETWORK_UK]),
//                self::getItem('/recipe/review/search'),
//            ];

        if(Membership::PKEY_AGENCY_HE == $mship)
            return [
//                self::getItem('/member/default/index'),
//                self::getItem('/member/toranoko/index'),　とらのこ入会はまだリリースしない
                self::getItem('/he/wholesale'),
                self::getItem('/pointing/he/index'),
//                Html::a("販売店・取扱所様専用注文入力", ['/he/wholesale']),
//                Html::a("販売店・取扱所様専用売上入力", ['/pointing/he/index']),
            ];

        if(in_array($mship, [Membership::PKEY_AGENCY_HJ_A,
                             Membership::PKEY_AGENCY_HJ_B])
        )
            return [
//                self::getItem('/member/default/index'),
                self::getItem('/hj/wholesale'),
                self::getItem('/pointing/hj/index'),
//                Html::a("販売店・取扱所様専用注文入力", ['/hj/wholesale']),
//                Html::a("販売店・取扱所様専用売上入力", ['/pointing/hj/index']),
                self::getItem('/cart/remedy/compose'),
                self::getItem('/cart/remedy/machine'),
            ];

        if(in_array($mship,[Membership::PKEY_STUDENT_INTEGRATE,
                            Membership::PKEY_STUDENT_TECH_COMMUTE,
                            Membership::PKEY_STUDENT_TECH_ELECTRIC])
        )
            return [
                self::getItem('/recipe/default/index'),
                self::getItem ('/recipe/create/search?target=client'),
                self::getItem('/cart/remedy/compose'),
                self::getItem('/cart/remedy/machine'),
            ];

        if(Membership::PKEY_HOMOEOPATH == $mship)
            return [
                self::renderExpireDate(Membership::PKEY_HOMOEOPATH),
//                self::getItem('/member/toranoko/index'),  とらのこ入会はまだリリースしない
                self::getItem('/recipe/default/index'),
                self::getItem ('/recipe/create/search?target=client'),
                self::getItem('/cart/remedy/compose'),
                self::getItem('/cart/remedy/machine'),
            ];

        if(Membership::PKEY_JPHMA_TECHNICAL == $mship)
            return [
                self::renderExpireDate(Membership::PKEY_JPHMA_TECHNICAL),
                self::getItem('/recipe/default/index'),
                self::getItem('/cart/remedy/compose'),
                self::getItem('/cart/remedy/machine'),
            ];

        // 現役本部ホメオパスかどうかで判定
        if($mship == Membership::PKEY_CENTER_HOMOEOPATH && Homoeopath::find()->active()->andWhere(['homoeopath_id' => $this->customer->customer_id])->one())
            return [
                self::getItem('/sodan/admin/index'),
            ];

        return [];
    }

    private static function renderExpireDate($mships)
    {
        $edate = CustomerMembership::find()->where(['customer_id'   => Yii::$app->user->id,
                                                    'membership_id' => $mships            ])
                                           ->max('expire_date');

        $forever_date = "2100/01/01 00:00:00";
//        会員資格　有効期限はしばらくの間表示しない
//        if(strtotime($edate) < strtotime($forever_date) ) {
//            $msg   = sprintf('<strong>会員資格は %s まで有効です</strong>',
//                             Yii::$app->formatter->asDate($edate));
//
//            return Html::tag('p', $msg, ['class'=>'alert alert-info']);
//        } else {
            return;
//        }
    }

    private static function getItem($route)
    {
        if('/he/wholesale' == $route)
            return Html::a("取扱所様専用注文入力",['/he/wholesale'])
                 . Html::tag('span','こちらから、商品をご注文いただくことができます。　<a href="'.Url::to(['/site/howtoorder_he']).'">★★★<BLINK><FONT COLOR=red>ご利用ガイドはこちら</FONT></BLINK>★★★</a>', ['class'=>'col-md-offset-1 small for-smart-phone']);

        if('/hj/wholesale' == $route)
            return Html::a("販売店様専用注文入力",['/hj/wholesale'])
                 . Html::tag('span','こちらから、商品をご注文いただくことができます。  <a href="'.Url::to(['/site/howtoorder_hj']).'">★★★<BLINK><FONT COLOR=red>ご利用ガイドはこちら</FONT></BLINK>★★★</a>', ['class'=>'col-md-offset-1 small for-smart-phone']);

        if('/hp/wholesale' == $route)
            return Html::a("取扱店様専用注文入力",['/hp/wholesale'])
                 . Html::tag('span','こちらから、商品をご注文いただくことができます。　 <a href="'.Url::to(['/site/howtoorder_hp']).'">★★★<BLINK><FONT COLOR=red>ご利用ガイドはこちら</FONT></BLINK>★★★</a>', ['class'=>'col-md-offset-1 small for-smart-phone']);

        if('/pointing/he/index' == $route)
            return Html::a("取扱所様専用売上入力",['/pointing/he/index'])
                 . Html::tag('span','こちらから、商品を販売していただくことができます。ポイント付与とご使用はこちらから。<a href="'.Url::to(['/site/howtosell_he']).'">★★★<BLINK><FONT COLOR=red>New！ご利用ガイドはこちら</FONT></BLINK>★★★</a>', ['class'=>'col-md-offset-1 small for-smart-phone']);

        if('/pointing/hj/index' == $route)
            return Html::a("販売店様専用売上入力",['/pointing/hj/index'])
                 . Html::tag('span','こちらから、商品を販売していただくことができます。ポイント付与とご使用はこちらから。<a href="'.Url::to(['/site/howtosell_hj']).'">★★★<BLINK><FONT COLOR=red>New！ご利用ガイドはこちら</FONT></BLINK>★★★</a>', ['class'=>'col-md-offset-1 small for-smart-phone']);

        if('/pointing/hp/index' == $route)
            return Html::a("取扱店様専用売上入力",['/pointing/hp/index'])
                 . Html::tag('span','こちらから、商品を販売していただくことができます。ポイント付与とご使用はこちらから。<a href="'.Url::to(['/site/howtosell_hp']).'">★★★<BLINK><FONT COLOR=red>New！ご利用ガイドはこちら</FONT></BLINK>★★★</a>', ['class'=>'col-md-offset-1 small for-smart-phone']);

        if('/cart/remedy/compose' == $route)
            return Html::a("オリジナルレメディーの購入",['/cart/remedy/compose'])
                 . Html::tag('span','ご自分でオリジナルレメディーを組み立て、ご注文いただくことができます。　<a href="'.Url::to(['/site/howtomk_original']).'">★★★オリジナルレメディーの組み立て方法はこちら★★★</a>', ['class'=>'col-md-offset-1 small for-smart-phone']);

        if('/cart/remedy/machine' == $route)
            return Html::a("特別レメディーの購入",['/cart/remedy/machine'])
                 . Html::tag('span','レメディーマシンによるレメディーの製造をご用命の際、ご注文いただくことができます。', ['class'=>'col-md-offset-1 small for-smart-phone']);

        if('/cms/oasis/index' == $route)
            return Html::a("会報誌「オアシス」",['/cms/oasis/index'])
                 . Html::tag('span','最新のネットオアシスを閲覧できます', ['class'=>'col-md-offset-1 small']);

        if('/member/default/index' == $route)
            return Html::a("豊受モール会員", ['/member/default/index'])
                 . Html::tag('span','豊受モール会員の検索、入会手続きにご利用ください。', ['class'=>'col-md-offset-1 small']);

        if('/member/toranoko/index' == $route)
            return Html::a("とらのこ会員", ['/member/toranoko/index'])
                 . Html::tag('span','とらのこ会員の検索・とらのこ入会手続き　などにご利用できます', ['class'=>'col-md-offset-1 small']);

        if('/recipe/review/index' == $route)
            return Html::a("適用書の検索", ['/recipe/review/index'])
                 . Html::tag('span','日本ホメオパシーセンターの健康相談で作成された適用書を閲覧します', ['class'=>'col-md-offset-1 small for-smart-phone']);

        if('/recipe/default/index' == $route)
            return Html::a("適用書", ['/recipe/default/index'])
                 . Html::tag('span','適用書を作成します。　　　　<a href="'.Url::to(['/site/howtomk_tekiyosyo']).'">★★★適用書の作成方法はこちら★★★</a>', ['class'=>'col-md-offset-1 small for-smart-phone']);

        if('/recipe/create/search?target=client' == $route)
            return Html::a("クライアントの検索", ['/recipe/create/search?target=client'])
                 . Html::tag('span','クライアントを検索します', ['class'=>'col-md-offset-1 small']);

        if('/sodan/admin/index' == $route)
            return Html::a("健康相談", ['/sodan/admin/index'])
                 . Html::tag('span','日本ホメオパシーセンター本部の健康相談についてカルテを作成・閲覧します', ['class'=>'col-md-offset-1 small']);

        if('sodan-upload' == $route)
        {
            return Html::a("健康相談の手続き", ['/profile/sodan/index'])
                 . Html::tag('span','日本ホメオパシーセンター本部の健康相談の予約手続きに必要な、同意書や質問票をダウンロードします', ['class'=>'col-md-offset-1 small']);
        }

        if('/debit/index' == $route)
        {
            if('echom-frontend' == Yii::$app->id) {
                return Html::a("口座振替登録",'https://mall.toyouke.com/index.php/profile/debit/index',['target' => '_blank'])
                    . Html::tag('span','こちらから、口座振替登録を行います', ['class'=>'col-md-offset-1 small for-smart-phone']);
            }
            return Html::a("口座振替登録",['/profile/debit/index'])
                . Html::tag('span','こちらから、口座振替登録を行います', ['class'=>'col-md-offset-1 small for-smart-phone']);
        }
        if('/default/view' == $route)
        {
            if('echom-frontend' == Yii::$app->id) {
                return Html::a("会員情報の確認・変更",'https://mall.toyouke.com/index.php/profile/default/view',['target' => '_blank'])
                    . Html::tag('span','会員情報の確認はこちらから', ['class'=>'col-md-offset-1 small for-smart-phone']);
            }
            return Html::a("会員情報の確認・変更",['/profile/default/view'])
                . Html::tag('span','会員情報の確認はこちらから', ['class'=>'col-md-offset-1 small for-smart-phone']);
        }     
        throw new \yii\base\InvalidParamException("invalid key is supplied: $route");
    }

}

