<?php

// 'maintenance.php' is a low-tech database management tool

require_once('util_fns.php');

session_start();

$cxn=db_connect();
if(mysqli_connect_errno()) {
	echo "Error: Could not connect to database.";
	exit;
}

// PROCEED ONLY IF USER IS 'owner'
if($_SESSION['valid_user'] != "owner") {
	exit;
	$cxn->close();
}

//print all table elements
print_elements('meds_table',$cxn);
print_elements('objective_table',$cxn);
print_elements('subjective_table',$cxn);
print_elements('vitals_table',$cxn);
print_elements('history_table',$cxn);
print_elements('radiology_table',$cxn);

/*****************************************************/
// function print_elements prints out all element names
// separated by newline
// parameter: $tablename corresponds to a databse table
// parameter: $cxn is the db connection handle
/*****************************************************/
function print_elements($tablename,$cxn_in) {

	$query_table="select name from ".$tablename;
	$result_table=$cxn_in->query($query_table);
	$num_rows=$result_table->num_rows;
	
	echo "*************** ".strtoupper($tablename)." ***************<br><br>";

	for($i=0;$i<$num_rows;$i++) {
		$rows=$result_table->fetch_assoc();
		$name=$rows['name'];
		echo $name."<br>";
	}

	echo "<br><br><br>";


} //close 'print_elements'


?>
	
