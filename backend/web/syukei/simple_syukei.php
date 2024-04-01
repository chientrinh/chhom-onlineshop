<?php

//

if(($_SERVER["REMOTE_ADDR"] != '106.186.235.1') 
&& ($_SERVER["REMOTE_ADDR"] != '210.191.40.254')
&& ($_SERVER["REMOTE_ADDR"] != '192.168.0.11')
&& ($_SERVER["REMOTE_ADDR"] != '210.196.149.174')
&& ($_SERVER["REMOTE_ADDR"] != '221.115.236.250')){
	print"外部からはアクセス出来ません。";
	exit;
}

$m = $_POST[m];
if($m == '')$m = $_GET[m];

if($m == ""){ #トップページにアクセス
	top_dsp();
}
elseif($m == 'org_data_dsp'){
	org_data_dsp();
}

function org_data_body_dsp() {
$link = mysqli_connect('localhost', 'root', '5IM2tWhA', 'mall');
//printf("Initial character set: %s\n", mysqli_character_set_name($link));

if (!mysqli_set_charset($link, "utf8")) {
    printf("Error loading character set utf8: %s\n", mysqli_error($link));
    exit();
}


// 接続状況をチェックします
if (mysqli_connect_errno()) {
    die("データベースに接続できません:" . mysqli_connect_error() . "\n");
}

//echo "データベースの接続に成功しました。\n";

//  echo '<p>' . $data['商品名'] . ': ' . $data['売値'] . ' :' . "</p>\n";

// 拠点後とのコングレスの売り上げを取得する
$reg_f = 0;
$campaign_sql = "(";
for($i=0;$i<10;++$i){
	if($_POST[campaign_id][$i] != ''){
		if($reg_f == 1)$campaign_sql .= " or ";
		$campaign_sql .= "p.campaign_id = ".$_POST[campaign_id][$i];
		$reg_f = 1;
	}
}
$campaign_sql .= ") AND ";
if($reg_f == 0){
	$campaign_sql = "";
}

$query = "
SELECT
  p.purchase_id AS '伝票ID'
  , p.status AS 'ステータス'
  , p.create_date AS '売上日'
  , p.customer_id AS '顧客ID'
  , CONCAT(c.name01, c.name02) AS '名前'
  , pi.campaign_id AS 'キャンペーンID'
  , pi.product_id AS '商品ID'
  , pi.remedy_id AS 'レメディーID'
  , pi.code AS 'コード'
  , pi.name AS '商品名'
  , pi.company_id AS '販社ID'
  , pi.quantity AS '数量'
  , pi.price AS '定価'
  , pi.unit_price AS '売値'
  , pi.unit_tax AS '税'
  , pi.discount_amount AS '値引額'
  , pi.discount_rate AS '値引率'
  , pi.point_amount AS 'ポイント付与'
  , pi.point_rate AS 'ポイント率'
  , p.branch_id AS '拠点ID'
  , pr.category_id AS 'カテゴリID'
FROM
  dtb_purchase p
  LEFT JOIN dtb_purchase_item pi
    ON p.purchase_id = pi.purchase_id
  LEFT JOIN dtb_customer c
    ON c.customer_id = p.customer_id
  LEFT JOIN dtb_product pr
    ON pi.product_id = pr.product_id
WHERE
  $campaign_sql
  p.create_date BETWEEN '".$_POST[s_year]."-".$_POST[s_month]."-".$_POST[s_day]." 00:00:00' AND '".$_POST[e_year]."-".$_POST[e_month]."-".$_POST[e_day]." 23:59:59'
;
";

//print $query;
// クエリを実行します。
if ($result = mysqli_query($link, $query)) {
// print "'伝票ID','ステータス','売上日','顧客ID','名前','キャンペーンID','商品ID','レメディーID','コード','商品名','販社ID','数量','定価','売値','税','値引額','値引率','ポイント付与','ポイント率'<br>\n";
 $playList = fopen("org_data.csv", "w+");
 $logdt = "伝票ID,ステータス,売上日,顧客ID,名前,キャンペーンID,商品ID,レメディーID,コード,商品名,販社ID,数量,定価,売値,税,値引額,値引率,ポイント付与,ポイント率,拠点ID,カテゴリID\n";
 $logdt = mb_convert_encoding($logdt,"SJIS","UTF-8");
 fwrite($playList, $logdt);
 while ($data = mysqli_fetch_array($result, MYSQLI_BOTH)) {
//  echo $data['伝票ID'].",".$data['ステータス'].",".$data['売上日'].",".$data['顧客ID'].",".$data['名前'].",".$data['キャンペーンID'].",".$data['商品ID'].",".$data['レメディーID'].",".$data['コード'].",".$data['商品名'].",".$data['販社ID'].",".$data['数量'].",".$data['定価'].",".$data['売値'].",".$data['税'].",".$data['値引額'].",".$data['値引率'].",".$data['ポイント付与'].",".$data['ポイント率'] . "<br>\n";
  $logdt = $data['伝票ID'].",".$data['ステータス'].",".$data['売上日'].",".$data['顧客ID'].",".$data['名前'].",".$data['キャンペーンID'].",".$data['商品ID'].",".$data['レメディーID'].",".$data['コード'].",".$data['商品名'].",".$data['販社ID'].",".$data['数量'].",".$data['定価'].",".$data['売値'].",".$data['税'].",".$data['値引額'].",".$data['値引率'].",".$data['ポイント付与'].",".$data['ポイント率'].",".$data['拠点ID'].",".$data['カテゴリID'] . "\n";
  $logdt = mb_convert_encoding($logdt,"SJIS","UTF-8");
	fwrite($playList, $logdt);
	$ttl_price = $data['売値'] * $data['数量'];
//	$sql = "insert into dtb_purchase_item_calc(name,unit_price,price,quantity,ttl_price,company_id) values('".$data['商品名']."','".$data['定価']."','".$data['売値']."','".$data['数量']."','".$ttl_price."','".$data['販社ID']."');";
//	mysqli_query($link, $sql);
//print $sql."<br>";
//	mysqli_query($link, "delete from dtb_purchase_item_calc;");
 }
 fclose($playList);
	$now_time = time();
	echo '<p style="text-align: center"><a href="org_data.csv?'.$now_time.' download="">生データCSVをダウンロード</a></p>';
}

// 接続を閉じます
$db = mysqli_close($link);

if (!$db) {
  exit('データベースとの接続を閉じられませんでした。');
}
}

exit;

function top_dsp() {
	header_dsp();
	menu_dsp();
	footer_dsp();
}

function org_data_dsp() {
	header_dsp();
	menu_dsp();
	org_data_body_dsp();
	footer_dsp();
}

function header_dsp() {
?>
<HTML><META content="text/html; charset=utf8" http-equiv="Content-Type">
<BODY bgcolor="#CCCCCC">
<?php
}

function footer_dsp() {
?>
</BODY>
</HTML>
<?php
}

function menu_dsp() {
?>

<form action="simple_syukei_test.php" method="post">
  <font size="2">
  <select name=s_year>
    <option selected>2020
    <option>2019
    <option>2018
    <option>2017
    <option>2016
    <option>2015
    <option>2014
    <option>2013
    <option>2012
    <option>2011
    <option>2010
    <option>2009
    <option>2008
    <option>2007
    <option>2006 
    <option>2005 
    <option>2004 
    <option>2003  
  </select>
  <select name=s_month>
    <option selected>01 
    <option>02 
    <option>03 
    <option>04 
    <option>05 
    <option>06 
    <option>07 
    <option>08 
    <option>09 
    <option>10 
    <option>11 
    <option>12 
  </select>
  <select name=s_day>
    <option>01 
    <option>02 
    <option>03 
    <option>04 
    <option>05 
    <option>06 
    <option>07 
    <option>08 
    <option>09 
    <option selected>10 
    <option>11 
    <option>12 
    <option>13 
    <option>14 
    <option>15 
    <option>16 
    <option>17 
    <option>18 
    <option>19 
    <option>20 
    <option>21 
    <option>22 
    <option>23 
    <option>24 
    <option>25 
    <option>26 
    <option>27 
    <option>28 
    <option>29 
    <option>30 
    <option>31 
  </select>
  ～ 
  <select name=e_year>
    <option selected>2020
    <option>2019
    <option>2018
    <option>2017
    <option>2016
    <option>2015
    <option>2014
    <option>2013
    <option>2012
    <option>2011
    <option>2010
    <option>2009
    <option>2008
    <option>2007
    <option>2006 
    <option>2005 
    <option>2004 
    <option>2003  
  </select>
  <select name=e_month>
    <option selected>01 
    <option>02 
    <option>03 
    <option>04 
    <option>05 
    <option>06 
    <option>07 
    <option>08 
    <option>09 
    <option>10 
    <option>11 
    <option>12 
  </select>
  <select name=e_day>
    <option>01 
    <option>02 
    <option>03 
    <option>04 
    <option>05 
    <option>06 
    <option>07 
    <option>08 
    <option>09 
    <option selected>10 
    <option>11 
    <option>12 
    <option>13 
    <option>14 
    <option>15 
    <option>16 
    <option>17 
    <option>18 
    <option>19 
    <option>20 
    <option>21 
    <option>22 
    <option>23 
    <option>24 
    <option>25 
    <option>26 
    <option>27 
    <option>28 
    <option>29 
    <option>30 
    <option>31 
  </select>
期間指定生データ出力<br>
キャンペーンID：（未入力の場合は全データ対象）
  <input type=input name=campaign_id[0] value="" size=4>
  <input type=input name=campaign_id[1] value="" size=4>
  <input type=input name=campaign_id[2] value="" size=4>
  <input type=input name=campaign_id[3] value="" size=4>
  <input type=input name=campaign_id[4] value="" size=4>
  <input type=input name=campaign_id[5] value="" size=4>
  <input type=input name=campaign_id[6] value="" size=4>
  <input type=input name=campaign_id[7] value="" size=4>
  <input type=input name=campaign_id[8] value="" size=4>
  <input type=input name=campaign_id[9] value="" size=4><br>
  <input type=hidden name=m value="org_data_dsp">
  <input type=submit value="生CSVデータを出力" name=org>
  </font>
</form>

<?php
}

?>


