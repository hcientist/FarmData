<?php session_start();?>
<?php
$farm = $_SESSION['db'];
if ($farm != 'dfarm') {
   $dbcon = mysql_connect('localhost', 'wahlst_usercheck', 'usercheckpass') or 
       die ("Connect Failed! :".mysql_error());
   mysql_select_db('wahlst_users');
   $sql="select username from users where dbase='".$_SESSION['db']."'";
   $result = mysql_query($sql);
   echo mysql_error();
   $useropts='';
   while ($row = mysql_fetch_array($result)) {
      $useropts.='<option value="'.$row['username'].'">'.$row['username'].'</option>';
   }
}

include $_SERVER['DOCUMENT_ROOT'].'/authentication.php';
include $_SERVER['DOCUMENT_ROOT'].'/design.php';
include $_SERVER['DOCUMENT_ROOT'].'/connection.php';
include $_SERVER['DOCUMENT_ROOT'].'/stopSubmit.php';

$origPileID = $_GET['pileID'];

$sqlget = "SELECT pileID, comments, active FROM compost_pile where pileID='".$origPileID."'";
$sqldata = mysql_query($sqlget) or die(mysql_error());
$row = mysql_fetch_array($sqldata);

$pileID = $row['pileID'];
$comments = $row['comments'];
$active = escapehtml($row['active']);

if ($active == 1) {
	$activeText = "Yes";
} else {
	$activeText = "No";
}
?>

<?php
echo "<form name='form' method='post' action=\"".$SERVER['PHP_SELF'].
   "?tab=admin:admin_delete:deletesoil:deletecompost&pileID=".encodeURIComponent($pileID)."\">";

echo "<H3> Edit Compost Record </H3>";
echo '<br clear="all"/>';

echo '<label>Pile ID:&nbsp</label>';
echo "<input type='text' id='pileID' name='pileID' value=\"".$pileID."\" class='textbox25'>";
echo "<br clear='all'>";

echo "<label>Active:&nbsp</label>";
echo "<div class='styled-select'>";
echo "<select name='active' id='active' class='mobile-select'>";
echo "<option value='".$activeText."'>".$activeText."</option>";
echo "<option value='Yes'>Yes</option>";
echo "<option value='No'>No</option>";
echo "</select>";
echo "</div>";
echo "<br clear='all'>";

echo '<label>Comments:&nbsp</label>';
echo '<br clear="all"/>';
echo "<textarea rows=\"10\" cols=\"30\" name = \"comments\" id = \"comments\">";
echo $comments;
echo "</textarea>";
echo '<br clear="all"/>';
echo '<br clear="all"/>';


echo "<input type='submit' name='submit' value='Update Record' class = 'submitbutton'>";
echo "</form>";
if ($_POST['submit']) {
	$comments = escapehtml($_POST['comments']);
	$pileID = escapehtml($_POST['pileID']);
	$active = escapehtml($_POST['active']);
	if ($active === "Yes") {
		$active = 1;
	} else {
		$active = 0;
	}

	$sql = "UPDATE compost_pile
		SET pileID='".$pileID."', comments='".$comments."', active=".$active." 
		WHERE pileID='".$origPileID."'";
   $result = mysql_query($sql);
   
	if(!$result){
       echo "<script>alert(\"Could not update data: Please try again!\\n".mysql_error()."\");</script>\n";
   } else {
      echo "<script>alert(\"Entered data successfully!\");</script> \n";
      echo '<meta http-equiv="refresh" content="0;';
     echo 'URL=compostPileTable.php?tab=admin:admin_delete:deletesoil:deletecompostpile">';
   }
}
?>