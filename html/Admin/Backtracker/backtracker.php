<?php session_start(); ?>
<?php
include $_SERVER['DOCUMENT_ROOT'].'/Admin/authAdmin.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/connection.php';
include $_SERVER['DOCUMENT_ROOT'].'/design.php';
include $_SERVER['DOCUMENT_ROOT'].'/stopSubmit.php';
$farm = $_SESSION['db'];
?>


<h1 style="font-size:52px; margin-top:-30px;"> Backdater  </h1>
<form name='form' method='POST' action='<?php  $_SERVER['PHP_SELF']?>'>

<label for="table"><b>Select Table:&nbsp;</b></label>
<div id='table2' class='styled-select'>
<select name='tableSelector' id='tableSelector' onChange='createTableHeader();'>
<option value='default' disabled selected>&nbsp;</option>
<option value='dir_planted'>Direct Seeding</option>
<option value='harvested'>Harvesting</option>
<option value='gh_seeding'>Flats Seeding</option>
<option value='transferred_to'>Transferred To</option>
</select>
</div>


<?php
/*$tables = mysql_query("show tables from ".$farm);

   while ($row = mysql_fetch_array($tables)) {
      echo "\n <option value='".$row[Tables_in_dfarm]."'> ".$row[Tables_in_dfarm]."</option>";
   }
        echo "</select></div>";

*/
?>

<br clear="all">





<script type="text/javascript">

var table;
var tableName;
var tableIndex;
var numRows;
var primary_key_string;

var fields_array;
var tableSize;

// Months of the year
var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

// Elements that must have numerical input
var numericalFields = ["bedft", "hours", "yield", "numseeds_planted", "flats"];

// Elements that need a dropdown menu
var dropdownArray = ["crop", "fieldID", "cellsFlat", "rowsBed"];

// Elements that are dates that should be split up into M/D/Y to edit
var dateArray = ["plantdate", "transdate", "hardate", "seedDate"];

// Elements that have to wait for other input into the form to be editable (generated by AJAX)
var ajaxArray = ["seedDate", "unit", "fieldID"];

/*************************************************************************
* Generates the table to be edited                                       *
*************************************************************************/
function createTableHeader() {
   if (!changeTableWarning()) {
      var selection = document.getElementById('tableSelector'); 
      selection.selectedIndex = tableIndex;
      return;
   }

   // tableName: the name of the table in the database
   // tableIndex: index of the selected option in the table select menu
   xmlhttp = new XMLHttpRequest();
   var a = document.getElementById("tableSelector");
   tableName = a.options[a.selectedIndex].value;
   tableIndex = a.selectedIndex;
   table = document.getElementById('myTable');

   // fields_array: Array of column names
   // tableSize: number of columns in table
   // First element in fields_array is column for primary keys
   xmlhttp.open("GET", "create_table_header.php?tableName="+tableName, false);
   xmlhttp.send();
   fields_array = eval(xmlhttp.responseText);
   tableSize = fields_array.length;

   // primary_key_string: String representing attributes in primary key
   // Formatted as "key1, key2, key3..."
   xmlhttp.open("GET", "get_primary_key_attributes.php?tableName="+tableName, false);
   xmlhttp.send();
   primary_key_string = eval(xmlhttp.responseText);

   // Create table header
   numRows = 1;
   if (table.tHead != null) {
      table.deleteTHead();
   }
   var header = table.createTHead();
   header.style.backgroundColor = "wheat";
   var row = header.insertRow(0);
   
   // Row Number
   cell = row.insertCell(0);
   cell.innerHTML = "Row";
   cell.style.width = "4%";

   // Table Data
   for (i = 1; i < tableSize; i++) {
      var cell = row.insertCell(i);
      cell.innerHTML = fields_array[i];
      if (dateArray.indexOf(fields_array[i]) > -1) {
         cell.style.width = "20%";
      }
   }

   // Delete Button
   cell = row.insertCell(tableSize);
   cell.innerHTML = "Delete";
   cell.style.width = "4%";
   
   // Copy Button
   cell = row.insertCell(tableSize + 1);
   cell.innerHTML = "Copy";
   cell.style.width = "4%";

   // Create Insert Rows Button
   var insertButton = document.getElementById('insertRowsButtonDiv');
   
   insertButton.innerHTML = "<form method='POST' action=''>" + 
      "<input type='button' class='largesubmitbutton' id='insertRows' name='insertRows' value='Submit Data to Database'" +
      "onclick='insertAllRows();'></form>";

   // Create New Row Buttons
   var buttonsTop = document.getElementById('buttonsTopDiv');
   var buttonsBottom = document.getElementById('buttonsBottomDiv');

   buttonsTop.innerHTML = "<input type='button' class='submitbutton' id='newRow' name='newRow' value='New Row'" + 
      "onclick='copyTheRow(-1);'>";
   buttonsTop.innerHTML += "<input type='button' class='submitbutton' id='copyRow' name='copyRow' value='Copy Last Row'" + 
      "onclick='copyTheRow(0);'>";

   buttonsBottom.innerHTML = "<input type='button' class='submitbutton' id='newRow' name='newRow' value='New Row'" +
      "onclick='copyTheRow(-1);'>";
   buttonsBottom.innerHTML += "<input type='button' class='submitbutton' id='copyRow' name='copyRow' value='Copy Last Row'" +
      "onclick='copyTheRow(0);'>";

   // Creates first row
   copyTheRow(-1);
}

/*************************************************************************
* If data exists in the current table, alert user that changing tables   *
*    will result in data being forgotten                                 *
* If user still wants to change table, return true, else return false    *
*************************************************************************/
function changeTableWarning() {
   for (i = 1; i <= numRows; i++) {
      if (document.getElementById("row" + i) != null) {
         if (document.getElementById("row" + i).innerHTML != "") {
            return confirm("Changing tables will get rid of the data in your current table!\n" + 
               "Are you sure you want to leave this table?");
         }
      }
   }
   return true;
}

/*************************************************************************
* Copies the data from the last row in the table and creates a new row   *
* rowNum: the index of the row to be copied                              *
*    - if rowNum == 0, copy the last row                                 *
*    - if rowNum == -1, add a new row that is not a copy                 *
*************************************************************************/
function copyTheRow(rowNum) {
   var table = document.getElementById('myTable');
   var row = table.insertRow(-1);
   row.id = "row" + numRows;
   row.style.backgroundColor = "white";

   // Row Number Column
   cell = row.insertCell(0);
   cell.innerHTML = numRows;

   // Add Input Boxes
   for (i = 1; i < tableSize; i++) {
      // Get the data from the appropriate cell
      var cell = row.insertCell(i);
      var data;

      // If rowNum is non-negative, copy a row
      if (rowNum >= 0) {
         // Look for the last cell
         if (rowNum == 0) {
            var j = numRows - 1;
            while (data == null && j >= 0) {
            
               if (dateArray.indexOf(fields_array[i]) > -1) {
                  monthData = document.getElementById(fields_array[i] + j + "month");
                  if (monthData != null) {
                     data = 1;
                     monthData = monthData.value;
                  }
               dayData = document.getElementById(fields_array[i] + j + "day");
                  if (dayData != null) {
                     dayData = dayData.value;
                  }
               yearData = document.getElementById(fields_array[i] + j + "year");
                  if (yearData != null) {
                     yearData = yearData.value;
                  }
               } else {
                  data = document.getElementById(fields_array[i] + j);
            
                  if (data != null) {
                     data = data.value;
                  }
               }
               j--;
            }
   
            // If data is still null, assign it empty string
            if (data == null) {
               data = "";
               var today = new Date();
               monthData = months[today.getMonth()];
               dayData = today.getDate();
               yearData = today.getFullYear();
            }
         } else {
            if (dateArray.indexOf(fields_array[i]) > -1 && ((ajaxArray.indexOf(fields_array[i]) < 0) ||
               !(tableName === "transferred_to" && fields_array[i] === "seedDate"))) {
                  monthData = document.getElementById(fields_array[i] + rowNum + "month");
                  monthData = monthData.value;
                  dayData = document.getElementById(fields_array[i] + rowNum + "day");
                  dayData = dayData.value;
                  yearData = document.getElementById(fields_array[i] + rowNum + "year");
                  yearData = yearData.value;
            } else {
               data = document.getElementById(fields_array[i] + rowNum);
               data = data.value;
            }
         }
      } else {
         data = "";
         var today = new Date();
         monthData = months[today.getMonth()];
         dayData = today.getDate();
         yearData = today.getFullYear();
      }
   
      // Check if the field calls for a dropdown menu
      // Special cases when AJAX is used to generate option values
      if (dropdownArray.indexOf(fields_array[i]) > -1 || ((ajaxArray.indexOf(fields_array[i]) > -1) &&
         (fields_array[i] === "seedDate" && tableName === "transferred_to") ||
         (fields_array[i] === "unit" && tableName === "harvested") ||
         (fields_array[i] === "fieldID" && tableName === "harvested"))) {
         cell.innerHTML = createDropdown(fields_array[i], data, numRows);
      } else if (dateArray.indexOf(fields_array[i]) > -1) {
         cell.innerHTML = createDateDropdown(fields_array[i], monthData, dayData, yearData, numRows);
      } else {
         cell.innerHTML = "<div id='" + fields_array[i] + numRows + "div' name='" + fields_array[i] + numRows + "div'>" + 
            " <input type='text' style='width:100%;'" + 
            " onchange='stopSubmitOnEnter(event);'" +
            " name='" + fields_array[i] + numRows + "' id='" + fields_array[i] + numRows + "'" +
            " class='textbox25' value='" + data + "'></div>";
      }

      data = null;
   }

   // Delete Button   
   cell = row.insertCell(tableSize);
   cell.innerHTML = "<input type='button' class='deletebutton' value='Delete'" +
      "onclick='deleteButton(" + numRows + ");'>";
   // Copy Button
   cell = row.insertCell(tableSize + 1);
   cell.innerHTML = "<input type='button' class='addbutton' value='Copy'" +
      "onclick='copyTheRow(" + numRows + ");'>";

   numRows++;
   return false;
}

/*************************************************************************
* Deletes a row from the table                                           *
* rowNum: the index of the row to be deleted                             *
*************************************************************************/
function deleteButton(rowNum) {
   var rowName = document.getElementById("row" + rowNum);
   rowName.innerHTML = "";
}

/*************************************************************************
* Creates a dropdown menu (select menu) for certain fields               *
* fieldname: name of the field (ex. fieldID, crop, unit)                 *
* data: if copying from a previous row, data is the value of what is     *
*    in the specified row's input box.                                   *
*    - if not copying from a row or value is null, data=""               *
* rowNum: the index of the row                                           *
*************************************************************************/
function createDropdown(fieldname, data, rowNum){
   var selectMenu = "";
   var xmlhttp = new XMLHttpRequest();
   var extraOptions;
   var dropdownArray;

   // Special cases when AJAX is used to generate option values
   if (fieldname === "unit" && tableName == "harvested") {
      var a = document.getElementById("crop" + rowNum);
      var cropName = a.options[a.selectedIndex].value;
                if ('<?php echo $farm;?>' === 'wahlst_spiralpath') {
         xmlhttp.open("GET", "/Harvest/hupdatesp.php", false);
                } else {
         xmlhttp.open("GET", "/Harvest/hupdate.php?crop="+
           encodeURIComponent(cropName), false);
                }
      xmlhttp.send();
      extraOptions = xmlhttp.responseText;
   } else if (fieldname === "fieldID" && tableName == "harvested") {
      var a = document.getElementById("crop" + rowNum);
      var cropName = a.options[a.selectedIndex].value;
      var b = document.getElementById("hardate" + rowNum + "year");
      var plantYear = b.options[b.selectedIndex].value;
      xmlhttp.open("GET", "/Harvest/update_field.php?crop="+
         encodeURIComponent(cropName)+"&plantyear="+plantYear, false);
      xmlhttp.send();
      extraOptions = xmlhttp.responseText;
   } else if (fieldname === "seedDate" && tableName === "transferred_to") {
      var a = document.getElementById("crop" + rowNum);
      var cropName = a.options[a.selectedIndex].value;
      xmlhttp.open("GET", "/Seeding/update_trans.php?crop=" +
         encodeURIComponent(cropName), false);
      xmlhttp.send();
      extraOptions = xmlhttp.responseText;
   // For when AJAX isn't used
   } else {
      xmlhttp.open("GET", "create_dropdown.php?fieldName=" +
         encodeURIComponent(fieldname)+"&tableName"+tableName, false);
      xmlhttp.send();
      dropdownArray = eval(xmlhttp.responseText);
   }

   selectMenu += "<div class='styled-select' id='" + fieldname + rowNum + "div' name='" + fieldname + rowNum + "div'>" + 
      "<select onchange='addInput(" + rowNum + ", \"" + fieldname + "\");' style='width:100%' name='" + fieldname + rowNum + "' id='" + fieldname + rowNum + "'>";
   selectMenu += "<option disabled value='" + data + "' selected>" + data + "</option>";

   if (extraOptions != null) {
      selectMenu += extraOptions;
   } else {
      for (q = 0; q < dropdownArray.length; q++) {
         selectMenu += "<option value='" + dropdownArray[q] + "'>" + dropdownArray[q] + "</option>";
      }
   }

   selectMenu += "</select></div>";
   
   return selectMenu;
}

/*************************************************************************
* Creates a dropdown menu (select menu) for date fields                  *
* fieldname: name of the field (ex. hardate, plantdate)                  *
* monthData: if copying from a previous row, monthData  is the value     *
*    of the month in the specified row                                   *
* dayData: if copying from a previous row, dayData is the value of       *
*    the day in the specified row                                        *
* yearData: if copying from a previous row, yearData is the value of     *
*    the year in the specified row                                       *
* rowNum: the index of the row                                           *
*************************************************************************/
function createDateDropdown(fieldname, monthData, dayData, yearData, rowNum) {
   var selectMenu = "";

   // Month   
   selectMenu += "<div class='styled-select' id='" + fieldname + rowNum + "monthdiv' name='" + fieldname + rowNum + "monthdiv'>" + 
      "<select onchange='addInput(" + rowNum + ", \"" + fieldname + "month\");' name='" + fieldname + rowNum + "month' id='" + fieldname + rowNum + "month'>";
   selectMenu += "<option value='" + monthData + "' selected>" + monthData + "</option>";
   for (mth = 0; mth < 12; mth++) {
      selectMenu += "<option value='" + months[mth] + "'>" + months[mth] + "</option>";
   }
   selectMenu += "</select></div>";

   // Day
   selectMenu += "<div class='styled-select' id='" + fieldname + rowNum + "daydiv' name='" + fieldname + rowNum + "daydiv'>" + 
      "<select onchange='addInput(" + rowNum + ", \"" + fieldname + "day\");' name='" + fieldname + rowNum + "day' id='" + fieldname + rowNum + "day'>";
   selectMenu += "<option value='" + dayData + "' seleted>" + dayData + "</option>";
   for (dy = 1; dy < 32; dy++) {
      selectMenu += "<option value='" + dy + "'>" + dy + "</option>";
   }
   selectMenu += "</select></div>";

   // Year
   var currYear = new Date().getFullYear();
   selectMenu += "<div class='styled-select' id='" + fieldname + rowNum + "yeardiv' name='" + fieldname + rowNum + "yeardiv'>" + 
      "<select onchange='addInput(" + rowNum + ", \"" + fieldname + "year\");' name='" + fieldname + rowNum + "year' id='" + fieldname + rowNum + "year'>";
   selectMenu += "<option value='" + yearData + "' selected>" + yearData + "</option>";
   for (yr = (currYear - 5); yr < (currYear + 4); yr++) {
      selectMenu += "<option value='" + yr + "'>" + yr + "</option>";
   }
   selectMenu += "</select></div>"; 

   return selectMenu;
}

/*************************************************************************
* AJAX adds data when appropriate                                        *
* rowNum: the index of the row                                           *
* fieldName: name of the field (ex. crop, fieldID)                       *
*************************************************************************/
function addInput(rowNum, fieldName) {
   var xmlhttp = new XMLHttpRequest();
   var referenceTable;
   var getField;

   if (tableName === "dir_planted") {
      
   } else if (tableName === "harvested") {
      // unit
      if (fieldName === "crop") {
         referenceTable = "units";
         getField = "unit";
   
         var a = document.getElementById("crop" + rowNum);
         var cropName = a.options[a.selectedIndex].value;

         if ('<?php echo $farm;?>' === 'wahlst_spiralpath') {
              xmlhttp.open("GET", "/Harvest/hupdatesp.php", false);
         } else {
            xmlhttp.open("GET", "/Harvest/hupdate.php?crop="+
               encodeURIComponent(cropName), false);
         }
         xmlhttp.send();

         var newElem = document.getElementById("unit" + rowNum + "div");
         newElem.innerHTML = "<div class='styled-select' id='unit" + rowNum + "div' name='unit" + rowNum + "div'>" +
            "<select style='width:100%' name='unit" + rowNum + "' id='unit" + rowNum + "'>" + 
            xmlhttp.responseText + "</select></div>";
      }
      // fieldID
      if (fieldName === "crop" || fieldName === "hardateyear") {
         referenceTable = null;
         getField = "fieldID";

         var a = document.getElementById("crop" + rowNum);
         var cropName = a.options[a.selectedIndex].value;
         var b = document.getElementById("hardate" + rowNum + "year");
         var plantYear = b.options[b.selectedIndex].value;
      
         xmlhttp.open("GET", "/Harvest/update_field.php?crop="+
            encodeURIComponent(cropName)+"&plantyear="+plantYear, false);
         xmlhttp.send();

         var newElem = document.getElementById("fieldID" + rowNum + "div");
         newElem.innerHTML = "<div class='styled-select' id='fieldID" + rowNum + "div' name='fieldID" + rowNum + "div'>" + 
            "<select style='width:100%' name='fieldID" + rowNum + "' id='fieldID" + rowNum + "'>" +
            xmlhttp.responseText + "</select></div>";
      }
   } else if (tableName === "gh_seeding") {
      
   } else if (tableName === "transferred_to") {
      // seedDate
      if (fieldName === "crop") {
         referenceTable = null;
         getField = "seedDate";

         var a = document.getElementById("crop" + rowNum);
         var cropName = a.options[a.selectedIndex].value;

         xmlhttp.open("GET", "/Seeding/update_trans.php?crop="+
             encodeURIComponent(cropName), false);
         xmlhttp.send();

         var newElem = document.getElementById("seedDate" + rowNum + "div");
         newElem.innerHTML = "<div class='styled-select' id='seedDate" + rowNum + "div' name='seedDate" + rowNum + "div'>" +
            "<select style='width:100%' name='seedDate" + rowNum + "' id='seedDate" + rowNum + "'>" +
            xmlhttp.responseText + "</select></div>";
      }
   }
}

/*************************************************************************
* Inserts all the rows in the table into the database                    *
*************************************************************************/
function insertAllRows() {
   if (!show_confirm()) {
      return;
   }

   var xmlhttp = new XMLHttpRequest();
   var unit;
   var crop;
   var yield;
   var conversion;

   for (i = 1; i <= numRows; i++) {

      // Check that rows exist
      if (document.getElementById("row" + i) != null) {
         if (document.getElementById("row" + i).innerHTML != "") {

            var values = [];
            for (j = 0; j < tableSize-1; j++) {
               
               if ((dateArray.indexOf(fields_array[j+1]) < 0) || 
                  (fields_array[j+1] == "seedDate" && tableName == "transferred_to")) {
                  var elem = document.getElementById(fields_array[j+1] + i);
                  //elem = escapeHtml(elem.value);
                  elem = elem.value;
                  values[j] = elem;

                  if (fields_array[j+1] == "unit") unit = elem;
                  if (fields_array[j+1] == "crop") crop = elem; 
                  if (fields_array[j+1] == "yield") yield = elem;
               } else {
                  var dayElem = document.getElementById(fields_array[j+1] + i + "day");
                  dayElem = dayElem.value;
                  var monthElem = document.getElementById(fields_array[j+1] + i + "month");
                  monthElem = monthElem.value;
                  monthElem = new Date(monthElem + "1, 2000").getMonth() + 1;
                  var yearElem = document.getElementById(fields_array[j+1] + i + "year");
                  yearElem = yearElem.value;
                  values[j] = yearElem + "-" + monthElem + "-" + dayElem;
               }
            }

            // Performs conversion for harvested table
            if (tableName === "harvested" && 
                '<?php echo $farm;?>' != "wahlst_spiralpath") {
               xmlhttp.open("GET", "convert_unit.php?crop="+
                  encodeURIComponent(crop)+"&unit="+
                  encodeURIComponent(unit), false);
               xmlhttp.send();
               defaultUnit_conversion = eval(xmlhttp.responseText);
               values[fields_array.indexOf("yield") - 1] = yield/defaultUnit_conversion[1];
               values[fields_array.indexOf("unit") - 1] = escapeescapeHtml(defaultUnit_conversion[0]);
            }

            values_array_json = JSON.stringify(values);
            fields_array_json = JSON.stringify(fields_array);
            xmlhttp.open("GET", "insert_row.php?tableName="
     +tableName+"&tableSize="+(tableSize-1)+"&fields_array="+
     fields_array_json+"&values_array="+encodeURIComponent(values_array_json),
    false);
            xmlhttp.send();
            if (xmlhttp.responseText != "") {
               alert("Error in MySQL inserting row into table:\n" + xmlhttp.responseText);
               //return false;
            }
         }
      }   
   }

   alert("Successfully entered data into table!");
   location.reload(true);
}

/*************************************************************************
* Check if all fields have data in them                                  *
* If there is a field with no data in it, tell user which row            *
*    and return false                                                    *
* If all fields have data, return true                                   *
*************************************************************************/
function show_confirm() {
   for (i = 1; i <= numRows; i++) {

      // Check that rows exist
      if (document.getElementById("row" + i) != null) {
         if (document.getElementById("row" + i).innerHTML != "") {

            for (j = 0; j < tableSize-1; j++) {
               var ele;
 
               if ((dateArray.indexOf(fields_array[j+1]) < 0) || 
                  (fields_array[j+1] == "seedDate" && tableName == "transferred_to")) {
                  if (fields_array[j+1] != "comments" && fields_array[j+1] != "varieties" && fields_array[j+1] != "numseeds_planted") {
                     ele = document.getElementById(fields_array[j+1] + i);
                     if (checkEmpty(ele.value) || ele.value == "" || ele.value == "undefined") {
                        alert("Check Row " + i + ": " + fields_array[j+1] + "\n" + 
                              "There is no value in the input field");
                        return false;
                     }
                  }
               } else {
                  ele = document.getElementById(fields_array[j+1] + i + "day");
                  if (checkEmpty(ele.value) || ele.value == "" || ele.value == "undefined") {
                     alert("Check Row " + i + ": " + fields_array[j+1] + " - Day\n" + 
                           "There is no value in the input field");
                     return false;
                  }

                  ele = document.getElementById(fields_array[j+1] + i + "month");
                  if (checkEmpty(ele.value) || ele.value == "" || ele.value == "undefined") {
                     alert("Check Row " + i + ": " + fields_array[j+1] + " - Month\n" + 
                           "There is no value in the input field");
                     return false;
                  }

                  ele = document.getElementById(fields_array[j+1] + i + "year");
                  if (checkEmpty(ele.value) || ele.value == "" || ele.value == "undefined") {
                     alert("Check Row " + i + ": " + fields_array[j+1] + " - Year\n" + 
                           "There is no value in the input field");
                     return false;
                  }   
               }

               if (numericalFields.indexOf(fields_array[j+1]) > -1) {
                  ele = document.getElementById(fields_array[j+1] + i);
                  if (isNaN(ele.value)) {
                     alert("Check Row " + i + ": " + fields_array[j+1] + "\n" + 
                           "Value must be a number");
                     return false;
                  }
               }
            }
         }
      }   
   }
   return confirm("Are you sure you want to input this data into the database?");
}

</script>




<br clear="all"/>
<div id="insertRowsButtonDiv"></div>
<br clear="all"/>
<div id="buttonsTopDiv"></div>
<br clear="all"/>
<table style="width:99%" id="myTable"></table>
<br clear="all"/>
<div id="buttonsBottomDiv"></div>

</form>