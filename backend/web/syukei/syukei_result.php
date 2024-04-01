<?php

$server_name = "localhost";
$user_name = "root";
$password = "5IM2tWhA";
$serve_db = "mall";

$allow_ip_address =
[   '106.186.235.1',
    '118.70.13.177',
    '210.191.40.254',
    '122.216.200.107',
    '210.196.149.174',
    '117.6.128.233',
    '171.224.177.141'	
];
#if(!array_search($_SERVER["REMOTE_ADDR"], $allow_ip_address)){
if(in_array($_SERVER["REMOTE_ADDR"], $allow_ip_address) === false){
    print"外部からはアクセス出来ません。";
    exit;
}

$connectDB = mysqli_connect($server_name, $user_name, $password, $serve_db);
//    printf("Initial character set: %s\n", mysqli_character_set_name($connectDB));
//    die;
//
if (!mysqli_set_charset($connectDB, "utf8")) {
    printf("Error loading character set utf8: %s\n", mysqli_error($connectDB));
    exit();
}

if (!$connectDB) {
    die("データベースに接続できません:" . mysqli_connect_error() . "\n");
}


function getStartAndEndDate($week, $year) {
    $dto = new DateTime();
    $dto->setISODate($year, $week);
    $ret['week_start'] = $dto->format('Y-m-d');
    $dto->modify('+2 days');
    $ret['start_date'] =  $dto->format('Y-m-d');
    $dto->modify('+6 days');
    $ret['end_date'] =  $dto->format('Y-m-d');
    return $ret;
}

$year = '年';
$month = '月';
$day = '日';
$week = '週';
$txt_date = '';
if($_POST) {
    $action_name = $_POST['action_name'];
    $branch_id = $_POST['branch_id'];
    $query_condition = '';
    switch ($action_name) {
        case 'syukei_daily' : ;
            $txt_date = $_POST['s_year'].$year.$_POST['s_month'] .$month.$_POST['s_day'].$day;
            $query_condition = "create_date BETWEEN '".$_POST['s_year']."-".$_POST['s_month']."-".$_POST['s_day']." 00:00:00' AND '" .$_POST['s_year']."-".$_POST['s_month']."-".$_POST['s_day']." 23:59:59'";
            break;
        case 'syukei_monthly' : ;
            $txt_date = $_POST['s_year'].$year. $_POST['s_month'] .$month;
            $query_condition = "YEAR(create_date) = " . $_POST['s_year']  ." AND MONTH(create_date) = " . $_POST['s_month'];
            break;
        case 'syukei_weekly' : ;
//            $txt_date = $_POST['s_year'].$year. $_POST['s_week'] .$week;
//            $query_condition = "YEARWEEK(create_date)=" .$_POST['s_year'].$_POST['s_week'];
//            break;
            $date_obj = getStartAndEndDate($_POST['s_week'], $_POST['s_year']);
            $start_week = $date_obj['start_date'];
            $end_week = $date_obj['end_date'];
            $txt_date = $_POST['s_year'].$year. $_POST['s_week'] .$week;
            $query_condition = "create_date BETWEEN '". $start_week ." 00:00:00' AND '". $end_week ." 23:59:59'";

            break;
        case 'syukei_free' : ;
            $txt_date = $_POST['s_year'].$year.$_POST['s_month'] .$month.$_POST['s_day'].$day
            . '～' . $_POST['e_year'].$year.$_POST['e_month'] .$month.$_POST['e_day'].$day;
            $query_condition = "create_date BETWEEN '".$_POST['s_year']."-".$_POST['s_month']."-".$_POST['s_day']." 00:00:00' AND '".$_POST['e_year']."-".$_POST['e_month']."-".$_POST['e_day']." 23:59:59'";
            break;
    }

$purchase_sql = "
SELECT
  purchase_id
FROM
  dtb_purchase
WHERE
  branch_id = '". $branch_id ."'
AND
  $query_condition
";

$purchase_data = [];
if($purchase_result = mysqli_query($connectDB, $purchase_sql)) {
    while ($data = $purchase_result->fetch_array(MYSQLI_ASSOC))
    {
        $purchase_data[] = $data['purchase_id'];
    }
}

$list_purchase_id = '';
if(count($purchase_data) > 0) {
    $list_purchase_id = "(" . join(",", $purchase_data) .")";
}

$company_list = [];
$total_price = 0;
$total_tax = 0;
$total_quantity = 0;
if($list_purchase_id) {
    $purchase_item_sql = "
    SELECT pi.name as product_name ,
        sum(pi.quantity * pi.price) as price,
        sum(pi.quantity) as quantity, 
        pi.product_id, 
        pi.purchase_id, 
        sum(pi.unit_tax) as tax,
        pi.company_id as company_id, 
        cy.name as company_name,
        pro.category_id as category_id
    FROM dtb_purchase_item pi
    LEFT JOIN mtb_company cy on cy.company_id = pi.company_id
    LEFT JOIN dtb_product pro on pro.product_id = pi.product_id
    WHERE pi.purchase_id IN $list_purchase_id
    GROUP BY pi.product_id
    ORDER BY company_id ASC, 
	          price DESC
    ";


    if($company_result = mysqli_query($connectDB, $purchase_item_sql)) {
        while ($data_item = $company_result->fetch_array(MYSQLI_ASSOC))
        {
            $company_list[$data_item['company_id']][] = $data_item;
        }

//        var_dump($company_list);
    }
}

//start get category
$category_data = [];
$category_sql = "
SELECT
  category_id, 
  name
FROM
  mtb_category
";

if($category_result = mysqli_query($connectDB, $category_sql)) {
    while ($category_item = $category_result->fetch_array(MYSQLI_ASSOC)) {
        $category_data[$category_item['category_id']] = $category_item['name'];
    }
}

//end get category

$db = mysqli_close($connectDB);

if (!$db) {
    exit('データベースとの接続を閉じられませんでした。');
}

}

?>

<HTML xmlns="http://www.w3.org/1999/html">
    <META content="text/html; charset=sjis" http-equiv="Content-Type">
    <BODY>
        <?php include_once 'syukei_header.php'; ?>

        <div align="left">■日付指定での集計
            <span> <?php echo $txt_date ?><br></span>

            <?php
                $total_price = 0;
                $total_tax = 0;
                $total_quantity = 0;
                if($company_list) {
                    foreach ($company_list as $company) {

            ?>
                <table border=1 width="600" align="center">
                    <thead>
                        <tr>
                            <td align="center" width="100">
                                <div align="center"><font size="2">商品名</font></div>
                            </td>
                            <td align="center" width="100">
                                <div align="center"><font size="2">商品合計額</font></div>
                            </td>
                            <td align="center" width="100">
                                <div align="center"><font size="2">消費税額</font></div>
                            </td>
                            <td align="center" width="100">
                                <div align="center"><font size="2">個数</font></div>
                            </td>
                            <td align="center" width="100">
                                <div align="center"><font size="2">会社</font></div>
                            </td>
                            <td align="center" width="100">
                                <div align="center"><font size="2">カテゴリ</font></div>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $company_total_price = 0;
                            $company_total_tax = 0;
                            $company_total_quantity = 0;
                            foreach ($company as $product_idx => $product) {
                                $product['tax'] = ($product['tax'] != NULL) ? $product['tax'] : 0;
                                $product['price'] = ($product['price'] != NULL) ? $product['price'] : 0;
                                $company_total_price += $product['price'];
                                $company_total_tax += $product['tax'];
                                $company_total_quantity += $product['quantity'];
                                $category_name = isset($category_data[$product['category_id']]) ? $category_data[$product['category_id']] : '';
                        ?>
                            <tr>
                                <td align="center" width="100" bgcolor="#CC9999">
                                    <div align="left"><font size="2"><?php echo $product['product_name']?></font></div>
                                </td>
                                <td align="center" width="100" bgcolor="#CC9999">
                                    <div align="center"><font size="2">￥<?php echo $product['price']?></font></div>
                                </td>
                                <td align="center" width="100" bgcolor="#CC9999">
                                    <div align="right"><font size="2">￥<?php echo $product['tax']?></font></div>
                                </td>
                                <td align="center" width="100" bgcolor="#CC9999">
                                    <div align="right"><font size="2"><?php echo $product['quantity']?></font></div>
                                </td>
                                <td align="center" width="100" bgcolor="#CC9999">
                                    <div align="right"><font size="2"><?php echo $product['company_name']?></font></div>
                                </td>
                                <td align="center" width="100" bgcolor="#CC9999">
                                    <div align="right"><font size="2"><?php echo $category_name?></font></div>
                                </td>
                            </tr>
                        <?php
                            }
                            $total_price += $company_total_price;
                            $total_tax += $company_total_tax;
                            $total_quantity += $company_total_quantity;
                        ?>

                        <tr>
                            <td align="center" width="100" bgcolor="#9999CC">
                                <div align="left"><font size="2">合計</font></div>
                            </td>
                            <td align="center" width="100" bgcolor="#9999CC">
                                <div align="center"><font size="2">￥<?php echo $company_total_price ?></font></div>
                            </td>
                            <td align="center" width="100" bgcolor="#9999CC">
                                <div align="right"><font size="2">￥<?php echo $company_total_tax ?></font></div>
                            </td>
                            <td align="center" width="100" bgcolor="#9999CC">
                                <div align="right"><font size="2"><?php echo $company_total_quantity ?></font></div>
                            </td>
                            <td align="center" width="100" bgcolor="#9999CC">
                                <div align="right"><font size="2"></font></div>
                            </td>
                            <td align="center" width="100" bgcolor="#9999CC">
                                <div align="right"><font size="2"></font></div>
                            </td>
                        </tr>
                    </tbody>
                </table></br>
            <?php
                    }
                }
            ?>

            <table border=1 width="600" align="center">
                <thead>
                    <tr>
                        <td align="center" width="100" bgcolor="#9999CC">
                            <div align="left"><font size="2">合計</font></div>
                        </td>
                        <td align="center" width="100" bgcolor="#9999CC">
                            <div align="center"><font size="2">￥<?php echo $total_price ?></font></div>
                        </td>
                        <td align="center" width="100" bgcolor="#9999CC">
                            <div align="right"><font size="2">￥<?php echo $total_tax ?></font></div>
                        </td>
                        <td align="center" width="100" bgcolor="#9999CC">
                            <div align="right"><font size="2"><?php echo $total_quantity ?></font></div>
                        </td>
                        <td align="center" width="100" bgcolor="#9999CC">
                            <div align="right"><font size="2"></font></div>
                        </td>
                        <td align="center" width="100" bgcolor="#9999CC">
                            <div align="right"><font size="2"></font></div>
                        </td>
                    </tr>
                </thead>
            </table>
        </div>
    </BODY>
</HTML>
