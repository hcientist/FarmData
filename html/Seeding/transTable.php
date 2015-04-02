<?php session_start();?>
<?php 
include $_SERVER['DOCUMENT_ROOT'].'/authentication.php';
include $_SERVER['DOCUMENT_ROOT'].'/design.php';
include $_SERVER['DOCUMENT_ROOT'].'/connection.php';
?>
<form name='form' method='POST' action='/down.php'>
<?php
$year = $_POST['year'];
$month = $_POST['month'];
$day = $_POST['day'];$tcurYear = $_POST['tyear'];
$tcurMonth = $_POST['tmonth'];
$tcurDay = $_POST['tday'];
$genSel = $_POST['genSel'];
$crop = escapehtml($_POST['transferredCrop']);
$fieldID = escapehtml($_POST['fieldID']);
$sql="Select fieldID,crop,seedDate,bedft,rowsBed,bedft * rowsBed as rowft,".
  " transdate,datediff(transdate,seedDate) as diffdate,flats, gen, hours, comments ".
  " from  transferred_to where crop like '".$crop."' and fieldID like '".
  $fieldID."' and gen like '".$genSel."' and transdate between '".$year."-".$month."-".$day.
  "' AND '".$tcurYear."-".$tcurMonth."-".$tcurDay."' order by transdate";
if ($crop != "%") {
   $sql2="select avg(diffdate) from (select datediff(transdate,seedDate) as ".
      "diffdate from transferred_to where crop = '".$crop."' and fieldID ".
      "like '".$fieldID."' and gen like '".$genSel."' and transdate between '".$year."-".$month."-".
      $day."' AND '".$tcurYear."-".$tcurMonth."-".$tcurDay."') as temp";
   $avg=mysql_query($sql2);
   echo mysql_error();
   $total = "select sum(bedft*rowsBed) as totalSum from transferred_to where ".
      "transdate between '".$year."-".$month."-".$day."' AND '".
      $tcurYear."-".$tcurMonth."-".$tcurDay."' AND crop = '".$crop."'".
      " and fieldID like '".$fieldID."' and gen like '".$genSel."'";
   $totalResult = mysql_query($total);
   echo mysql_error();
   $btotal = "select sum(bedft) as totalSum from transferred_to where ".
      "transdate between '".$year."-".$month."-".$day."' AND '".$tcurYear.
      "-".$tcurMonth."-".$tcurDay."' AND crop = '".$crop."'".
      " and fieldID like '".$fieldID."' and gen like '".$genSel."'";
   $btotalResult = mysql_query($btotal);
   echo mysql_error();
}

echo "<input type = \"hidden\" name = \"query\" value = \"".escapehtml($sql)."\">";
$result=mysql_query($sql);
echo mysql_error();
echo "<table>";
echo "<caption>Transplant Report for ";
if ($crop == "%") {
  echo "All Crops in ";
} else {
  echo $crop." in ";
}
if ($fieldID == "%") {
  echo "All Fields";
} else {
  echo "Field ".$fieldID;
}
if ($_SESSION['gens']) {
   if ($genSel == "%") {
      echo " of All Successions";
   } else {
      echo " of Succession ".$genSel;
   }
}
echo "</caption>";
   echo "<tr><th>Crop<center></th><th>Field</th><th>Date of Tray Seeding</th><th><center>Date of Transplanting</center></th><th><center>Days in Tray</center> </th><th>Bed Feet</th><th>Rows/Bed</th><th><center>Row Feet</center></th><th>Trays</th>";
if ($_SESSION['gens']) {
   echo "<th>Succ&nbsp;#</th>";
}
if ($_SESSION['labor']) {
   echo "<th>Hours</th>";
}
echo "<th><center> Comments</center></th></tr>";
   while ($row= mysql_fetch_array($result)) {
        echo "<tr><td>";
        echo $row['crop'];
        echo "</td><td>";
        echo $row['fieldID'];
        echo "</td><td>";
        //echo str_replace("-","/",$row['seedDate']);
        if ($row['seedDate'] == '0000-00-00') {
           echo "N/A";
        } else {
	   echo $row['seedDate'];
        }
        echo "</td><td>";
	//echo str_replace("-","/",$row['transdate']);
        echo $row['transdate'];
        echo "</td><td>";
        echo $row['diffdate'];
        echo "</td><td>";
        echo number_format((float) $row['bedft'], 1, '.', '');
	// echo $row['bedft'];
        echo "</td><td>";
	echo $row['rowsBed'];
        echo "</td><td>";
	// echo $row['rowft'];
        echo number_format((float) $row['rowft'], 1, '.', '');
        echo "</td><td>";
	echo $row['flats'];
        echo "</td><td>";
   if ($_SESSION['gens']) {
        echo $row['gen'];
	echo "</td><td>";
   }
   if ($_SESSION['labor']) {
        echo number_format((float) $row['hours'], 2, '.', '');
	echo "</td><td>";
   }
        echo $row['comments'];
        echo "</td></tr>";
   }
   echo "</table>";
   echo '<br clear="all"/>';
   if ($crop != "%") {
   while ($row2 = mysql_fetch_array($avg)) {
        $formatNum=number_format($row2['avg(diffdate)'],2,'.','');
	echo "<label for='average'>Average Days in Tray: &nbsp</label> <input style='width: 100px;' class='textbox2'type ='text' name='avgDays' disabled value=".$formatNum.">";
   }
   echo '<br clear="all"/>';
   while($row3 = mysql_fetch_array($btotalResult)) {
        echo "<label for='sum'>Total Number of Bed Feet Planted: &nbsp;</label> <input class='textbox3'type ='text' name='sum' disabled value=".$row3['totalSum'].">";
   }
   echo '<br clear="all"/>';
   while($row3 = mysql_fetch_array($totalResult)) {
        echo "<label for='sum'>Total Number of Row Feet Planted: &nbsp;</label> <input class='textbox3'type ='text' name='sum' disabled value=".$row3['totalSum'].">";
   }
   echo '<br clear="all"/>';
   echo '<br clear="all"/>';
}
echo '<input class="submitbutton" type="submit" name="submit" value="Download Report">';

if (!$result||(!$avg && $crop != "%")) {
        echo "<script>alert(\"Could not enter data: Please try again!\\n".mysql_error()."\");</script>\n";
}
echo '</form>';
echo '<form method="POST" action = "/Seeding/transplantReport.php?tab=seeding:transplant:transplant_report"><input type="submit" class="submitbutton" value = "Run Another Report"></form>';
?>
