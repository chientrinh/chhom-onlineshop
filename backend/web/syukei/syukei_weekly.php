<?php
require 'syukei_common.php';

$arr_day_previous_week = ['Monday', 'Tuesday'];
$current_date = date('y-m-d');
$date = new DateTime($current_date);
$week = $date->format("W");
$unixTimestamp = strtotime($current_date);
$dayOfWeek = date("l", $unixTimestamp);
$current_week = in_array($dayOfWeek, $arr_day_previous_week) ?  (int)$week -1 : $week;

?>


<HTML>
    <META content="text/html; charset=sjis" http-equiv="Content-Type">
    <BODY bgcolor="#CCCCCC">
        <?php include_once 'syukei_header.php'; ?>

        ■週次の集計<br><br>
        <form action="syukei">
            <font size="2">
                <select name=s_year>
                    <?php
                        for ($i = 2020; $i >= 2003; $i--) {
                    ?>
                        <option <?php echo date('Y') == $i ? 'selected' : '' ?>> <?php echo $i ?> </option>
                    <?php
                     }
                    ?>
                </select>
                　 第<select name=s_week>
                    <?php
                    for ($j = 1; $j <= 53; $j++) {
                        $selected = ($current_week == $j) ? 'selected' : '';
                        $week = $j;
                        if ($j < 10) {
                            $week = '0' . $j;
                        }
                    ?>
                        <option <?php echo $selected ?>> <?php echo $week ?></option>
                    <?php
                    }
                    ?>
                </select>週<br><br>
<?php if(0){ // delete button ?>
                <table>
                    <?php foreach ($branch_data as $branch) {?>
                        <tr>
                            <?php foreach ($branch as $branch_index => $branch_item) { ?>
                                <td><input id=<?php echo $branch_item['branch_id']  ?>  type="button" onclick="setData(this.id, this.value);" value="<?php echo $branch_item['name'] ?>"></td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </table>
<?php } ?>
<?php include("button_dsp.php"); // add button ?>
                <br>
            </font>
        </form>
        <form name="syukei_weekly" action="syukei_result.php" method="post">
            <input type="hidden" name="action_name" value="syukei_weekly">
            <input type="hidden" name="s_year" value="">
            <input type="hidden" name="s_week" value="">
            <input type="hidden" name="branch_id" value="">
            <input type="hidden" name="branch_name" value="">
        </form>
    </BODY>
    <script type="text/javascript">
        function setData(id, name) {
            document.querySelector('form[name="syukei_weekly"] input[name="s_year"]').value =  document.querySelector('select[name="s_year"]').value;
            document.querySelector('form[name="syukei_weekly"] input[name="s_week"]').value =  document.querySelector('select[name="s_week"]').value;
            document.querySelector('form[name="syukei_weekly"] input[name="branch_id"]').value =  id;
            document.querySelector('form[name="syukei_weekly"] input[name="branch_name"]').value =  name;
            document.querySelector('form[name="syukei_weekly"]').submit();
        }
    </script>
</HTML>
