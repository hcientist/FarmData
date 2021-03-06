<?php session_start(); ?>
<?php
include $_SERVER['DOCUMENT_ROOT'].'/authentication.php';
include $_SERVER['DOCUMENT_ROOT'].'/design.php';
include $_SERVER['DOCUMENT_ROOT'].'/connection.php';
include $_SERVER['DOCUMENT_ROOT'].'/stopSubmit.php';
include $_SERVER['DOCUMENT_ROOT'].'/Soil/clearForm.php';
?>
<form name='form' class='pure-form pure-form-aligned' id='form' 
   method='POST' action="<?php echo $_SERVER['PHP_SELF'];?>?tab=soil:soil_scout:soil_weed:weed_input"
   enctype="multipart/form-data">

<center>
<h2> Weed Scouting Input Form </h2>
</center>

<div class="pure-control-group">
<label for='date'> Date: </label>
<?php
if (isset($_POST['day']) && isset($_POST['month']) && isset($_POST['year'])) {
   $dDay = $_POST['day'];
   $dMonth = $_POST['month'];
   $dYear = $_POST['year'];
}
if (isset($_POST['fieldID'])) {
   $field = escapehtml($_POST['fieldID']);
}
include $_SERVER['DOCUMENT_ROOT'].'/date.php';
?>
</div>

<div class='pure-control-group'>
<label for="fieldID">Name of Field: </label>
<select name ="fieldID" id="fieldID" class="mobile-select">
<?php
   $result=$dbcon->query("Select fieldID from field_GH where active=1");
   while ($row1 = $result->fetch(PDO::FETCH_ASSOC)){
      echo "\n<option value= '".$row1[fieldID]."'";
      if (isset($field) && $field == $row1['fieldID']) {
         echo " selected";
      }
      echo ">".$row1[fieldID]."</option>";
   }
   echo '</select>';
   echo '</div>';
?>

<div class='pure-control-group'>
<label>Weed Species: </label>
<select name="species" id="species">
<?php
   $sql = 'Select weedName from weed';
   $result = $dbcon->query($sql);
   while ($row1 = $result->fetch(PDO::FETCH_ASSOC)){
      echo '<option value="'.$row1['weedName'].'">'.$row1['weedName'].'</option>';
   }      
?>
</select>
</div>

<div class='pure-control-group'>
<label>Infestation: </label>
<select name="infest" id="infest">
<option value="0">0</option>
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
</select>
</div>

<div class='pure-control-group'>
<label>% Gone to Seed: </label>
<select name="g2seed" id="g2seed">
<option value="0">0</option>
<option value="25">25</option>
<option value="50">50</option>
<option value="75">75</option>
<option value="100">100</option>
</select></div>

<!--
<input type="hidden" name="hid" id="hid">
<br clear="all"/>
<br clear="all" />
<table name="fieldTable" id="fieldTable" class="pure-table pure-table-bordered">
<thead>
<tr><th>Field Name</th><th>Weed Species</th><th>infestation</th><th>% to seed</th></tr>   
</thead>
<tbody>
</tbody>
</table>
<script type="text/javascript">
var numRows=0;
function addRow(){
   numRows++;
   var table = document.getElementById("fieldTable").getElementsByTagName('tbody')[0];
   var row = table.insertRow(-1);
   row.id = "row" + numRows;
   row.name = "row" + numRows;
   var cell0 = row.insertCell(0);
   cell0.innerHTML = '<div class="styled-select" id="fieldDiv'+numRows+'"> <select name="fieldID'+numRows+
                     '" id="fieldID'+numRows+'" class="wide">'+
                     '<option value= 0 selected disabled> Field Name </option>'+
                     '<?php
                         $result=$dbcon->query("select fieldID from field_GH where active=1");
                        while($row1 = $result->fetch(PDO::FETCH_ASSOC)){
                           echo '<option value="'.$row1[fieldID].'">'.$row1[fieldID].'</option>';
                        }
                      ?>' + '</select></div>';
   var cell1 = row.insertCell(1);
   cell1.innerHTML = '<div class="styled-select" id="speciesDiv'+numRows+'"> <select class="wide" name="species'+numRows+'" id="species'+numRows+'">'+
                     '<option value = 0 selected disabled>weed species</option>'+
                     '<?php
                        $sql = 'Select weedName from weed';
                        $result = $dbcon->query($sql);
                        while ($row1 = $result->fetch(PDO::FETCH_ASSOC)){
                           echo '<option value="'.$row1['weedName'].'">'.$row1['weedName'].'</option>';
                        }      
                      ?>' + '</select></div>';
   var cell2 = row.insertCell(2);
   cell2.innerHTML = '<div class="styled-select" id="infestDiv'+numRows+'"><select class="wide" name="infest'+numRows+'" id="infest'+numRows+'"><option value = 0 selected disabled> Infestation</option> <option>0</option> <option>1</option> <option>2</option> <option>3</option> </select></div>';
   var cell3 = row.insertCell(3);
   cell3.innerHTML = '<div class="styled-select" id="g2seedDiv'+numRows+'"><select class="wide" name="g2seed'+numRows+'" id="g2seed'+numRows+'"><option value = 0 selected disabled> %ToSeed</option> <option>0</option> <option>25</option> <option>50</option> <option>75</option> <option>100</option> </select></div>';

}
addRow();
function removeRow() {
   if (numRows > 0) {
      var field=document.getElementById('fieldID' + numRows);
      field.parentNode.removeChild(field);
      var species=document.getElementById('species' + numRows);
      species.parentNode.removeChild(species);
      var infest=document.getElementById('infest' + numRows);
      infest.parentNode.removeChild(infest);
      var g2seed=document.getElementById('g2seed' + numRows);
      g2seed.parentNode.removeChild(g2seed);
      var table = document.getElementById("fieldTable");
      table.deleteRow(numRows);
      numRows--;
   }
}
</script>
<br clear="all"/>
<div class="pure-g">
<div class="pure-u-1-2">
<input class = "submitbutton pure-button wide" type="button" id="add" value="Add Species" onclick="addRow();"/>
</div>
<div class="pure-u-1-2">
<input type="button" id="remove" class="submitbutton pure-button wide"  value="Remove Species" onClick="removeRow();"/>
</div>
</div>
<br clear="all"/>
-->

<div class="pure-control-group" id="filediv">
<label for="file">Picture (optional): </label>
<input type="file" name="fileIn" id="file">
</div>

<div class="pure-control-group">
<label for="clear">Max File Size: 2 MB </label>
<input type="button" value="Clear Picture" onclick="clearForm();">
</div>

<?php
if ($_SESSION['labor']) {
echo '
<div class="pure-control-group">
<label for="numWorkers">Number of workers (optional):</label>
<input onkeypress= \'stopSubmitOnEnter(event)\'; type = "text" value = 1 name="numW" id="numW"
  class="textbox2 mobile-input single_table">
</div>

<div class="pure-control-group">
<label>Enter time in Hours or Minutes:</label>
<input onkeypress=\'stopSubmitOnEnter(event);stopTimer();\' type="text" name="time" id="time"
class="textbox2 mobile-input-half single_table" value="1">
<select name="timeUnit" id="timeUnit" class=\'mobile-select-half single_table\' onchange="stopTimer();">
<option value="minutes">Minutes</option>
<option value="hours">Hours</option>
</select>
</div> ';

include $_SERVER['DOCUMENT_ROOT'].'/timer.php';
}
?>

<div class="pure-control-group">
<label for="comments"> Comments: </label>
<textarea name="comments" rows="5" cols="30" id="comments"></textarea>
</div>
<br clear="all"/>
<br clear="all"/>
<script type="text/javascript">
function show_confirm() {
   var fld = document.getElementById("fieldID").value;
   var con = "Field Name: " + fld + "\nScout Date: "
   var mth = document.getElementById("month").value;
   var dy = document.getElementById("day").value;
   var yr = document.getElementById("year").value;
   con += mth + "-" + dy + "-" + yr + "\n";;
   var species = document.getElementById("species").value;
   con += "Weed Species: " + species + "\n";
   var infest = document.getElementById("infest").value;
   con += "Infestation: " + infest + "\n";
   var toseed = document.getElementById("g2seed").value;
   con += "Percentage Gone to Seed: " + toseed + "\n";
   var fname = document.getElementById("file").value;
   if (fname != "") {
      var pos = fname.lastIndexOf(".");
      var ext = fname.substring(pos + 1, fname.length).toLowerCase();
      if (ext != "gif" && ext != "png" && ext != "jpg" && ext != "jpeg") {
         alert("Invalid image type: only gif, png, jpg and jpeg allowed.");
         return false;
      }
      con += "Picture: "+ fname + "\n";
   }

<?php
if ($_SESSION['labor']) {
   echo '
   var wk = document.getElementById("numW").value;
   if (checkEmpty(wk) || tme<=wk || !isFinite(wk)) {
      showError("Enter a valid number of workers!");
      return false;
   }
   con = con+"Number of workers: " + wk + "\n";
   var tme = document.getElementById("time").value;
   var unit = document.getElementById("timeUnit").value;
   if (checkEmpty(tme) || tme<=0 || !isFinite(tme)) {
      showError("Enter a valid number of " + unit + "!");
      return false;
   }
   con = con+"Number of " + unit + ": " + tme + "\n";';
}
?>

   var com = document.getElementById("comments").value;
   con += "Comments: "+ com + "\n";

   return confirm("Confirm Entry:"+"\n"+con);
}
</script>
<div class="pure-g">
<div class="pure-u-1-2">
<input type="submit" name="submit" class="submitbutton pure-button wide" value="Submit" onClick="return show_confirm();">
<?php
echo "</form>";
echo "</div>";
echo '<div class="pure-u-1-2">';
echo '<form method="POST" action = "weedReport.php?tab=soil:soil_scout:soil_weed:weed_report"><input type="submit" class="submitbutton pure-button wide" value = "View Table" onclick="return confirmLeave();"></form>';
echo "</div>";
echo "</div>";
if (isset($_POST['submit'])) {
   $comments = escapehtml($_POST['comments']);
   $var= $_POST['hid'];

   if ($_SESSION['labor']) {
      // Check if given time is in minutes or hours
      $time = escapehtml($_POST['time']);
      if ($_POST['timeUnit'] == "minutes") {
         $hours = $time/60;
      } else if ($_POST['timeUnit'] == "hours") {
         $hours = $time;
      }
      // Check if num workers is filled in
      $numW = escapehtml($_POST['numW']);
      if ($numW != "") {
         $totalHours = $hours * $numW;
      } else {
         $totalHours = $hours;
      }
   } else {
      $totalHours = 0;
   }

   include $_SERVER['DOCUMENT_ROOT'].'/Soil/imageUpload.php';

   $sql = "insert into weedScout(sDate, fieldID, weed, infestLevel, gonetoSeed,comments,hours,filename) ".
      "values ('".  $_POST['year']."-".$_POST['month']."-".$_POST['day'].
       "', :fieldID, :species, :infest, :g2seed,'". $comments."', ".$totalHours.", ";
   if ($fname == "null") {
      $sql .= "null";
   } else {
      $sql .= ":filename";
   }
   $sql .= ")";
   try {
      $stmt = $dbcon->prepare($sql);
      $fieldID = escapehtml($_POST['fieldID']);  
      $infest = escapehtml($_POST['infest']);
      $g2seed = escapehtml($_POST['g2seed']);
      $species = escapehtml($_POST['species']);
      $stmt->bindParam(':fieldID', $fieldID, PDO::PARAM_STR);
      $stmt->bindParam(':species', $species, PDO::PARAM_STR);
      $stmt->bindParam(':infest', $infest, PDO::PARAM_INT);
      $stmt->bindParam(':g2seed', $g2seed, PDO::PARAM_INT);
      if ($fname != "null") {
         $stmt->bindParam(':filename', $fname, PDO::PARAM_STR);
      }
      $stmt->execute();
   } catch (PDOException $p) {
      phpAlert('', $p);
      die();
   }
   echo "<script>showAlert(\"Data Entered Successfully!\\n\");</script>\n";
}
?>
