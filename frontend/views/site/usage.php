<?php
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/usage.php $
 * $Id: usage.php 1279 2015-08-13 03:15:33Z mori $
 *
 * @var $this \yii\web\View
 */

$title = "利用規約";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'Guide';
$this->title = implode(' | ',array_merge(array_reverse($this->params['breadcrumbs']),[Yii::$app->name]));

?>
<div class="site-guide">

    <h1 class="mainTitle"><?= Html::encode($title) ?></h1>

    <p class="mainLead">
この利用規約（以下、本規約）は、日本豊受自然農株式会社（以下、当社）管理の豊受オーガニクスモール（当社Webサイト）をご利用いただくにあたり、利用規約を設けさせていただいております。
    </p>

    <p>当社Webサイトを利用される前に、以下のサイト利用規約をお読み下さい。
当社Webサイトをご利用された場合、本ページに記載の利用規約にご同意いただけたものとさせていただきます。本ページに記載の利用規約は予告なく変更することがありますので、必ず最新情報をご確認ください。 
    </p>

    <h2>★著作権について</h2>

    <p>当社Webサイト内に掲載されている全ての内容（文章、商標、デザイン、コンテンツ等）における著作権、特許権、商標権等は、原則として当社あるいは各出店企業・提携団体に帰属します。
これらについて私的使用など認められた範囲を超えて、当社の許可無く複製、転用、改変等することは法律で禁止されています。
    </p>

    <h2>★サービスの内容及びサイト利用規約の内容変更・一時的な利用中止について</h2>
    <p>当社が必要と判断した場合には、事前に通知することなく当社Webサイトの運営に伴う内容の変更・利用中断、および本規約の変更を行う場合があります。内容変更後は、変更後の内容のみ有効とさせていただきます。
        また、当社Webサイトの内容の変更・利用中断、および本規約の変更に起因して生じたいかなる損害についても、当社は一切責任を負わないものとします。 </p>

    <h2>★免責事項</h2>
    <p>
当社Webサイトは日本豊受自然農株式会社の良識のもと十分な注意を払って編集・記載しておりますが、その内容について保証するものではありません。
記載された情報の有用性・正確性・安全性（コンピュータウィルスなどの有害性がないことやシステムエラーが発生しないことなど）に対して一切を保証するものではございません。また、当社サイトの利用及びその利用の不可能によって生じる損害について一切の責任を負いません。これについて、利用者は当社Webサイトへのアクセスをもって、同意したものとします。
当社は、当社Webサイトのサービスをいつでも任意の理由で中断することができます。当社は、当社Webサイトの利用、または利用ができないことによって引き起こされた直接的または間接的な損失、損害について、一切責任を負いません。
    </p>

    <div class="form-group text-center">
        <?= Html::a("★初めての方へ", ['about']) ?>
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <?= Html::a("★会員規約", ['usage','#'=>'member']) ?>
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <?= Html::a("★ポイントサービス利用規約", ['usage','#'=>'point']) ?>
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <?= Html::a("★プライバシー・ポリシー", ['policy']) ?>
    </div>

<?=nl2br("
<h2 id='member'>★会員規約</h2>

第１条　会員規約
本規約は、日本豊受自然農株式会社（以下「当社」）が運営するインターネットサイト「日本豊受自然農オーガニクスショッピングモール（豊受オーガニクスショッピングモール）」及び豊受オーガニクスショッピングモール出店企業の直営店、販売店（一部）において提供されるサービス（会員サービス）を、第４条で定義された会員が利用するにあたっての一切について適用されます。

第２条　本規約の範囲
1.本規約の本文以外に、「はじめての方へ」「ポイントサービス規約」「プライバシーポリシー」等の会員サービスに関する各種ルール、諸規定（諸規定等）、及び今後追加する諸規定等も、豊受オーガニクスショッピングモール会員規約を構成するものとします。
2.本規約の本文と諸規定等が異なる場合には、諸規定等の定めが優先して適用されるものとします。

第３条　本規約の変更
当社は、会員の承諾を得ることなく本規約を変更することができるものとし、当社が適当と判断する方法で会員に公表又は通知することにより、その効力を生じるものとします。この場合には、会員サービスの利用条件等は、変更後の会員規約によります。

第４条　会員
1.会員とは、第５条記載の方法により当社に会員サービスへの入会申し込みを行い、当社が承諾した者（「スタンダード会員」）、または別途定める方法により会員資格を取得した者（「スペシャル会員」、「スペシャルプラス会員」、「プレミアム会員」、「プレミアムプラス会員」）を言います。
2.会員は、会員サービスへの入会申し込みを行った時点で、あるいは会員資格を取得した時点で、本規約の内容をすべて承諾しているものとみなします。

第５条　入会
1.入会希望者は、豊受オーガニクスショッピングモール各規約の全てをお読みいただき、ご承諾いただいたうえで、豊受オーガニクスショッピングモールサイトから、もしくは豊受オーガニクスショッピングモール出店企業の直営店、販売店(一部)にて入会を申し込み、当社が入会を承諾した時点で会員（「スタンダード会員」）になるものとします。 
2.同一住所あるいは同一電話番号（携帯電話番号含む）での重複した新規会員登録は行うことは不可とします。家族会員としては登録できますが、家族会員用の会員証は発行いたしません。

第６条　会員サービスの利用 
1.会員は、豊受オーガニクスショッピングモールのすべての規約に従い、会員サービスを利用するものとします。
2.会員は、自己のID（自己のメールアドレスあるいは、当社より発行した会員番号）及びパスワード（自分で登録したパスワード）を使用することにより、豊受オーガニクスショッピングモールの会員サービスを利用することが可能です。
3.会員は、当社より希望者に送付、もしくは手渡された豊受オーガニクスショッピングモール会員証を使用することにより、豊受オーガニクスショッピングモール出店企業の直営店もしくは販売店（一部）において、各種サービス（ポイント付与・使用、イベント参加費割引（一部対象外）、限定商品の購入などの特典）を受けることができます。

第７条　ID、パスワード及び豊受オーガニクスショッピングモール会員証の管理責任
1.会員は、自己のID（自己のメールアドレスあるいは、会員番号）及びパスワード、そして希望者に発行された豊受オーガニクスショッピングモール会員証を自己の責任において管理するものとし、当社はID、パスワード及び豊受オーガニクスショッピングモール会員証の利用・管理等に関して会員に生じた損害について一切の責任を負わないものとします。
2.会員のID、パスワード及び豊受オーガニクスショッピングモール会員証によりなされた会員サービスの利用は、当該会員により行われたものとみなし、当該会員は商品代金の支払債務、その他の債務の一切を負担するものとします。
3.会員は、ID、パスワード及び豊受オーガニクスショッピングモール会員証を第三者が使用、また第三者に貸与、譲渡、名義変更、売買、質入れ等をしてはならないものとします。但し、家族会員として登録された方（同居のご家族に限る）の、親会員の会員証、ID・パスワードの利用は可能とします。
4.会員は、ID、パスワード及び豊受オーガニクスショッピングモール会員証の紛失・盗難があった場合、またはID、パスワード及び豊受オーガニクスショッピングモール会員証が第三者に使用されていることが判明した場合には、直ちに当社にその旨を連絡すると共に、当社からの指示がある場合にはこれに従うものとします。

第８条　変更の届け出
会員は、氏名、住所、電話番号、メールアドレス、その他会員情報に変更が生じた場合には、豊受オーガニクスショッピングモールに自己のIDおよびパスワードでログインし、速やかに会員情報を変更するものとします。

第９条　豊受オーガニクスショッピングモール会員証の再発行
会員が、豊受オーガニクスショッピングモール会員証を紛失するなどして、再発行を希望する場合は、有料（500円／税別）とします。なお、再発行の際に会員番号は変更となります。各社直営店もしくは販売店（一部）にて再発行を承ります。第５条に示したとおり、同一住所あるいは同一電話番号（携帯電話番号含む）での新規会員登録は不可。家族会員としては登録できますが、家族会員用の会員証は発行いたしません。

第10条　譲渡等の禁止
会員は、本規約に基づき会員として有する権利について、第三者に譲渡、売買、名義変更、質権の設定等の行為をしてはならないものとします。

第11条　退会
会員が退会する場合には、当社所定の方法によって当社に届け出るものとします。

第12条　会員資格の取り消し等 
1.会員が以下の(1)〜(9)のいずれかに該当する場合、当社は事前に通知をすることなく、直ちに当該会員の会員資格を取り消すことができるものとします。
　(1)公序良俗に反する行為、あるいは法令に違反する行為があった場合
　(2)豊受オーガニクスショッピングモール各規約に違反した場合
　(3)他の会員もしくは第三者を誹謗、中傷するなど他の会員もしくは第三者に不利益を与える行為があった場合
　(4)当社の運営や営業を妨害する行為があった場合
　(5)会員に付与されたID及びパスワード、会員証を不正使用した場合
　(6)当社への申告、届け出内容に虚偽があった場合
　(7)料金等の支払債務の履行遅延または不履行があった場合
　(8)電話、FAX、電子メール、その他の連絡手段によっても、会員との連絡が取れなくなった場合
　(9)その他、会員として不適切と当社が判断した場合
2.会員が上記の(1)〜(9)のいずれかに該当する場合、当社は、会員サービスの利用により締結された個別の契約や商品購入などを解除することができるものとします。
3.当社が前2項の措置を取ったことで、当該会員または第三者に損害が発生したとしても、当社は一切の責任を負わないものとします。

第13条　設備等
会員は、会員サービスを利用するために必要な通信機器、ソフトウエア、その他これに付随して必要となる全ての機器の準備および回線利用契約の締結、インターネット接続サービスへの加入、その他必要な準備を、自己の費用と責任において行うものとします。 

第14条　会員サービスの提供
会員サービスの内容は、当社がその時点で合理的に提供可能なものとします。当社は、理由の如何を問わず、会員に事前の通知をすることなく、会員サービスの内容の一部または全部の変更、追加および廃止をすることができるものとします。この場合、会員に不利益、損害が発生したとしても、当社は一切の責任を負わないものとします。 

第15条　会員サービス利用の中止
当社は、以下に該当する場合、会員の承諾を受けることなく会員サービスの利用の全部または一部を中止することがあります。この場合、会員に不利益、損害が発生したとしても、当社は一切の責任を負わないものとします。
　(1)当社の会員サービス用設備の保守上または工事上やむを得ない場合
　(2)12カ月間連続して会員サービス利用の実績がなかった場合
　(3)電気通信事業者が電気通信サービスを中止した場合
　(4)火災、地震、洪水、戦争、暴動その他の当社の責めに帰さない事由により会員サー
　ビスの提供ができなくなった場合
　(5)その他、会員サービスの運用上又は技術上の相当な理由がある場合  

第16条　著作権等
別段の定めのない限り、会員サービスのオンライン上での各コンテンツに関する著作権その他の知的財産権は、当社あるいは各コンテンツの提供者に帰属するものとし、また、各コンテンツの集合体としての会員サービスの著作権その他の知的財産権は、当社に帰属するものとします。これらについて当社の許可無く、私的使用など認められた範囲を超えて、複製、転用、改変等することは法律で禁止されています。

第17条　個人情報の収集・保有・管理・利用
会員は、当社が別途定めるプライバシーポリシーに基づき、会員の個人情報を取り扱うことに同意するものとします。

第18条　個人情報（会員情報）の閲覧・訂正
会員は、豊受オーガニクスショッピングモールに自己のIDおよびパスワードでログイン後、自身の会員登録情報の閲覧・変更・訂正ができるものとします。

第19条　個人情報の利用停止
個人情報の利用停止等の手続きについては、当社所定の方法によって当社に届け出るものとします。

第20条　免責事項
1.当社は、第三者が登録するデータ等について、その完全性、正確性、適用性、有用性、最新性、確実性、動作性、安全性等に関し、一切の保証を行わないものとします。
2.会員が本会員サービスに関して損害を被った場合であっても、その損害が当社の故意又は重過失による場合を除き、当社は会員サービスに関して会員又は第三者に生じた損害について一切の責任を負わないものとします。なお、本規約に別段の定めがあるときはその定めに従うものとします。
3.当社は、当社ウェブサイト上における第三者の提供する広告内容の正確性について、一切の保証を行わないこととし、当該広告によって会員に生じた損害に対し、一切の責任を負わないものとします。
4.当社ウェブサイトよりリンクする他のサイトについて、当社はそれらリンク先のサイトを利用することにより会員に生じた損害に対し、一切の責任を負わないものとします。

第21条　準拠法 
本規約の効力、解釈等に関しては、日本国法が適用されるものとします。 

第22条　協議事項 
本規約又は会員サービスに関連して問題が生じた場合には、会員様と当社双方で誠意をもって協議し、解決に努めるものとします。 

第23条　管轄裁判所 
本規約又は会員サービスに関連する一切の紛争について、東京地方裁判所を第一審の専属的合意管轄裁判所とします。
")?>

    <h2 id="point">
★ポイントサービス利用規約
    </h2>
<?=nl2br("
第１条（目的）
1.本規約は、日本豊受自然農株式会社（以下「当社」）が、豊受オーガニクスショッピングモール会員規約（以下「会員規約」）に基づき会員登録をした会員（以下「会員」）に対して、豊受ポイントサービス（以下「本サービス」）を提供するにあたり、その諸条件を定めるものです。
2.本サービスに関し本規約に規定のない事項については、豊受オーガニクスショッピングモール各規約が適用されます。

第２条（ポイントの付与）
1.会員が豊受オーガニクスショッピングモール、豊受オーガニクスショッピングモール出店企業の直営店あるいは販売店（一部）において、商品の購入やサービスを利用したとき、豊受ポイント（以下「ポイント」）を付与します（ポイント対象外の商品やサービスを除く）。
2.ポイント付与の対象となる商品、サービスおよび取引（以下「対象取引」）、ポイントの付与率、その他ポイント付与の条件は、会員区分によって異なり、各豊受オーガニクスショッピングモール出店企業が決定します。会員が各商品のポイントを確認する際は、豊受オーガニクスショッピングモールサイトに自己のIDとパスワードでログインし、商品情報として表示されるポイントを、自己で確認することとします。なお商品のポイントは、商品価格（税別）×ポイント付与率で決定し、小数点以下は切り捨てとします。

第３条（ポイントの管理）
会員が保有ポイントを確認したい場合は、豊受オーガニクスショッピングモールに自己のIDとパスワードでログインし、自己の保有ポイントを確認するものとします。会員は、前項のポイント数に疑義のある場合には、直ちに当社に連絡し、その内容を説明するものとします。

第４条（ポイントの譲渡等の禁止）
会員は、保有するポイントを他の会員に譲渡または質入れしたり、会員間でポイントを共有・共用を行うことはできません。

第５条（ポイントの取り消し・消滅）
1.当社はポイントを付与した後に、対象取引について返品、キャンセルその他、ポイントの付与を取り消すことが適当と判断する事由があった場合、対象取引により付与されたポイントを取り消すことができます。
2.会員が次の各号のいずれかに該当すると判断した場合、当社は会員に事前に通知することなく、会員が保有するポイントの一部または全てを取り消します。
　(1)違法または不正行為があった場合 
　(2)本規約、会員規約、その他当社が定める規約・ルール等に違反があった場合 
　(3)その他当社が会員に付与したポイントを取り消すことが適当と判断した場合 
3.会員は、当社が定める期間（１年）を超えて、ポイント対象取引を行わなかった場合、自動的に保有するポイントは消滅します。
4.当社が取り消したポイント及び消滅したポイントについては何らの補償・補填を負うことはなく、一切の責任をも負いません。

第６条（決済におけるポイントの利用）
1.会員は、保有するポイントを、当社が定める換算率（１ポイント＝１円）で、豊受オーガニクスショッピングモール、豊受オーガニクスショッピングモール出店企業の直営店もしくは販売店（一部）における商品代金（税抜）の全部または一部の支払いに、現金値引きとして利用することができます。
2.豊受オーガニクスショッピングモール出店企業は、第１項のポイント利用の対象となる商品・サービス等を制限したり、ポイント利用に条件を付したりすることがあります。
3.会員が第１項による決済を取り消した場合、原則として当該決済に利用されたポイントは返還され、現金による返還は行われません。
4.商品代金支払時にポイントを使った場合、使ったポイントに対しては、新たにポイントは付与されません。ただし、現金支払い分（商品代金［税抜］−ポイント）に対しては、相当のポイントが付与されます。例として1,000円（税抜）の商品購入において100ポイントを使用した場合、残りの900円（税抜）の現金支払いに対して、ポイントが付与されます。5%ポイント付与の会員の場合、現金支払い分の900円（税抜）に対して、900×0.05=45ポイントが付与されます。（小数点以下の端数が出た場合は切り捨てとなります）。もし複数の会社にまたがって商品を購入している場合は、各社で対象ポイントを計算し、合計します。

第７条（決済以外でのポイントの利用）
1.会員は、前条に定める店舗における代金支払時の現金値引きとしてのポイント利用のほか、保有するポイントを使用し、会員区別の変更、その他の特典やサービス（2015年7月現在未定）と交換することができます。
2.当社は、前項に定める事由により会員に何らかの不利益が発生したとしても、それらについて補償せず、一切の責任を負いません。

第８条（事故等）
第６条の決済対象となった商品または前条の特典につき、その配送中または提供後に遅延、紛失、盗難、損害、破損等の事故が生じた際、当該事故が当社の責任による場合を除き、当社は一切責任を負わず、ポイントの払い戻しも行いません。

第９条（換金の不可）
会員は、いかなる場合でもポイントを換金することはできません。

第１０条（第三者による使用）
1.ポイントの使用は、当該会員または家族会員以外の第三者が行うことはできません。
2.当社は、ポイント使用時に入力された会員のIDおよびパスワードが登録されたものと一致することを、当社が所定の方法により確認した場合、あるいは豊受オーガニクスショッピングモール会員証が提示された場合、会員による使用とみなします。それが第三者による不正使用であった場合でも、当社は使用されたポイントの返還をいたしません。会員に生じた損害について一切責任を負わないものとします。

第１１条（会員資格の喪失・停止）
会員が会員の資格を喪失した場合には、保有するポイント、特典との交換権、その他本サービスの利用に関する一切の権利を失うものとし、また会員資格の喪失について、当社に対して何らの請求権も保有しないものとします。

第１２条（免責）
当社は、本サービスの運用にその時点での技術水準を前提に最善を尽くしますが、障害が生じないことを保証するものではありません。通信回線やコンピューターなどの障害によるシステムの中断・遅滞・中止・データの消失、ポイント利用に関する障害、データへの不正アクセスにより生じた損害、その他本サービスに関して会員に生じた損害について、当社は一切責任を負わないものとします。

第１３条（本サービスの変更）
1.当社は、会員に事前に通知することなく、本規約、本サービスの内容または本サービス提供の条件の変更（ポイントサービスの廃止、ポイント付与・使用の停止、ポイント付与率の変更を含み、これらに限りません）を行うことがあり、本サービスを終了または停止することがあります。会員はこれらをあらかじめ承諾するものとします。
2.当社は、前項の変更により会員に不利益または損害が生じた場合でも、これらについて一切責任を負わないものとします。
")?>

</div>
