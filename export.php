<?php

 require 'export-all-queries-var.php';



// BEGIN: Define some variables
// INSTRUCTION: Specify your table name and the name of your export file.

// The name of data table containing the data you wish to export
$TableName = "Constant_States_Codes";

// The filename you want your export file to be named
$Filename = "Report_{$_POST['report-type']}_";
// END: Define some variables

// *** No more configurable options below this point for this code to function on most servers ***
// Fetch records from the database table specified in the variable $TableName
$Output = "";
$yearStr = $_POST['year'];




//Case to execute SQL string
if ($_POST['report-type'] == 're02') {
    $strSQL = $re02SQLstr;
} else if($_POST['report-type'] == 're03'){
    $strSQL = $re03SQLstr;

} else if($_POST['report-type'] == 're01s'){
    $strSQL = $re01sSQLstr;

}
else {
    $strSQL = $re01SQLstr;
}


$sql = mysqli_query($link_sqli, $strSQL);
// If the database query encounters an error, display the error message.
// Otherwise, start the export process.
if (mysqli_error($link_sqli)) {
    echo mysqli_error($link_sqli);
} else {
    // Determine the number of data columns in the table
    $columns_total = mysqli_num_fields($sql);

    // Get the name of the data columns so it can be used in the header row of the export file.
    // Content of the export file is temporarily saved in the variable $Output
    for ($i = 0; $i < $columns_total; $i++) {
        $Heading = mysqli_fetch_field_direct($sql, $i);
        $Output .= '"' . $Heading->name . '",';
    }
    $Output .= "\n";
    // The /n is the control code to go to a new line in the export file.

    // Loop through each record in the table and read the data value from each column.
    while ($row = mysqli_fetch_array($sql)) {
        for ($i = 0; $i < $columns_total; $i++) {
            $Output .= '"' . $row["$i"] . '",';
        }
        $Output .= "\n";
    }

    // Create the export file and name it with the name specified in variable $Filename
    // Also appends the current timestamp (in the format yyyymmddhhmmss) to the filename and give it a .CSV file extension.
    // The timestamp serves as a time reference to identify when the data was exported.
    //File is comma delimited with double-quote used a the text qualifier
    // Once  file is created, download of the file begins automatically (tested on Google Chrome).
    $TimeNow = date("YmdHis");
    $Filename .= $TimeNow . ".csv";


    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . $Filename);
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    echo "\xEF\xBB\xBF"; // UTF-8 BOM
    echo $Output;
}
exit;
