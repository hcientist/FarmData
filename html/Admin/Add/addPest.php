<?php session_start(); ?>
<?php
include $_SERVER['DOCUMENT_ROOT'].'/Admin/authAdmin.php';
include $_SERVER['DOCUMENT_ROOT'].'/design.php';
include $_SERVER['DOCUMENT_ROOT'].'/connection.php';
include $_SERVER['DOCUMENT_ROOT'].'/stopSubmit.php';
?>
<form name="form" method="post" action="<?php $_PHP_SELF ?>">
<h1><b>Add New Insect Species</b></h1>
<label for="covercrop"> Insect Species Name:&nbsp;</label>
<input class="textbox3 mobile-input" onkeypress= 'stopSubmitOnEnter(event)'; type="text" name="name" id="name">
<!--
<br clear="all"/>
<label for="admin">Active:&nbsp;</label>
<input style="margin-top: 10px;" type="checkbox"name="active" id="active" class="imgClass2 regular-checkbox big-checkbox"  /><label for="checkboxFiveInput"></label>
-->
<br clear="all"/>
<br clear="all"/>

<script>
function show_confirm() {
        var i = document.getElementById("name").value;
        var con="Species Name : "+ i+ "\n";


return confirm("Confirm Entry: " +"\n"+con);

}
</script>
<input onclick= "return show_confirm()";  class="submitbutton" type="submit" name="done" value="Add">
</form>
<?php
$active = 1;
if (!empty($_POST['done'])) {
/*
   if (!empty($_POST['active'])) {
      $active=1;
   }
*/
   if(!empty($_POST['name'])) {
      $name = escapehtml(strtoupper($_POST['name']));
      $sql="Insert into pest(pestName) values ('".$name."')";
      $result=mysql_query($sql);
      if (!$result) {
         echo "<script>alert(\"Could not add insect species: please try again!\\n".mysql_error()."\");</script>\n";
      } else {
         echo "<script>showAlert(\"Added insect species successfully!\");</script> \n";
      }
   } else {
      echo "<script>alert(\"Enter all data!\\n".mysql_error()."\");</script> \n";
   }
}
?>
