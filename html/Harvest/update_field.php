<?php
include $_SERVER['DOCUMENT_ROOT'].'/connection.php';

$crop = escapehtml($_GET['crop']);

$sql = "select fieldID from (SELECT fieldID from dir_planted natural join field_GH 
where active = 1 and crop like '".$crop."' and year(plantdate) = '".$_GET['plantyear']."' 
union 
SELECT fieldID from transferred_to natural join field_GH 
where active = 1 and crop = '".$crop."' and year(transdate) = '".$_GET['plantyear']."') as tmp
order by fieldID ";

$result = mysql_query($sql);
while($row = mysql_fetch_array($result)) {
echo "<option value=\"".$row['fieldID']."\">".$row['fieldID']."</option>";
}

mysql_close();
?>
