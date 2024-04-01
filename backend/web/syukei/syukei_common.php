<?php

$server_name = "localhost";
$user_name = "root";
$password = "5IM2tWhA";
$serve_db = "mall";

$allow_ip_address = ['106.186.235.1', '118.70.13.177', '210.191.40.254', '122.216.200.107', '210.196.149.174', '117.6.128.233', '171.224.177.141'];
if(in_array($_SERVER["REMOTE_ADDR"], $allow_ip_address) === false){
#if(!array_search($_SERVER["REMOTE_ADDR"], $allow_ip_address)){
print "aa".$_SERVER["REMOTE_ADDR"];
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

$query = "
SELECT
  branch_id,
  name
FROM
  mtb_branch
";

$result = mysqli_query($connectDB, $query);
$data = [];
$branch_data = [];
while ($list = $result->fetch_array(MYSQLI_ASSOC))
{
    $data[]= $list;
}


$total = count($data);
$col = 4;
$rows = ceil($total/$col);

for($i = 1; $i <= $rows; $i++) {
    for($j = 0; $j < $total; $j++){
        $max_record = $i*$col < $total ? $i*$col  : $total;
          if($j >= ($i-1)*$col && $j < $max_record) {
              $branch_data[$i][] = $data[$j];
          }
     }
}


$db = mysqli_close($connectDB);

if (!$db) {
    exit('データベースとの接続を閉じられませんでした。');
}


?>

