<?php session_start();?>

<form name='form' method='POST' action='distributionTable.php?tab=admin:admin_sales:distribution:distribution_report'>

<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/connection.php';
include $_SERVER['DOCUMENT_ROOT'].'/authentication.php';
include $_SERVER['DOCUMENT_ROOT'].'/design.php';
?>

<h3>Distribution Report</h3>
<br clear='all'>

<?php
echo "<label for='from'>From:&nbsp;</label>";
include $_SERVER['DOCUMENT_ROOT'].'/date.php';
echo "<br clear='all'>";
echo "<label for='to'>To:&nbsp;</label>";
include $_SERVER['DOCUMENT_ROOT'].'/date_transdate.php';
?>

<br clear='all'>
<label>Crop/Product:&nbsp;</label>
<div class='styled-select'>
<select name='crop_product' id='crop_product' class='mobile-select'>
<option value='%'>All</option>
<?php
$sql = "SELECT crop FROM plant WHERE active=1 union SELECT product FROM product";
$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
	echo "<option value='".$row[0]."'>".$row[0]."</option>";
}
?>
</select></div>

<br clear='all'>
<label>Target:&nbsp;</label>
<div class='styled-select'>
<select name='target' id='target' class='mobile-select'>
<option value='%'>All</option>
<?php
$sql = "SELECT targetName FROM targets";
$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
	echo "<option value='".$row[0]."'>".$row[0]."</option>";
}
?>
</select><div>

<br clear='all'>
<label>Grade:&nbsp;</label>
<div class='styled-select'>
<select name='grade' id='grade' class='mobile-select'>
<option value='%'>All</option>
<option value='1'>1</option>
<option value='2'>2</option>
<option value='3'>3</option>
<option value='4'>4</option>
</select></div>

<br clear='all'>
<br clear='all'>
<input class='submitbutton' type='submit' name='submit' value='Submit'>
</form>