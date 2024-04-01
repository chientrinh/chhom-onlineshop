<?php
require 'syukei_common.php';

?>
<HTML>
<META content="text/html; charset=sjis" http-equiv="Content-Type">

<BODY bgcolor="#CCCCCC">
    <?php include_once 'syukei_header.php'; ?>
    
    ■日次の集計<br><br>
    <form name="syukei_daily">
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
            <select name=s_month>
                <?php
                for ($i = 1;$i <= 12;$i++) {
                    $month = $i;
                    if ($i < 10) {
                        $month = '0' . $i;
                    }
                ?>
                    <option <?php echo date('m') == $month ? 'selected' : '' ?>> <?php echo $month ?></option>
                <?php
                    }
                ?>
            </select>
            <select name=s_day>
                <?php
                    for($i = 1; $i <= 31; $i ++) {
                    $day = $i;
                    if($i < 10) {
                        $day = '0'.$i;
                    }
                ?>
                    <option <?php echo date('d') == $day ? 'selected' : '' ?>> <?php echo $day ?> </option>
                <?php
                    }
                ?>
            </select><br><br>
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
    <form name="syukei_result" action="syukei_result.php" method="post">
        <input type="hidden" name="action_name" value="syukei_daily">
	<input type="hidden" name="s_year" value="">
        <input type="hidden" name="s_month" value="">
        <input type="hidden" name="s_day" value="">
        <input type="hidden" name="branch_id" value="">
        <input type="hidden" name="branch_name" value="">
    </form>
</BODY>
<script type="text/javascript">
    function setData(id, name) {
        document.querySelector('form[name="syukei_result"] input[name="s_year"]').value =  document.querySelector('select[name="s_year"]').value;
        document.querySelector('form[name="syukei_result"] input[name="s_month"]').value =  document.querySelector('select[name="s_month"]').value;
        document.querySelector('form[name="syukei_result"] input[name="s_day"]').value =  document.querySelector('select[name="s_day"]').value;
        document.querySelector('form[name="syukei_result"] input[name="branch_id"]').value =  id;
        document.querySelector('form[name="syukei_result"] input[name="branch_name"]').value =  name;
        document.querySelector('form[name="syukei_result"]').submit();
    }
</script>
</HTML>

