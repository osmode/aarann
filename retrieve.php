<?php
//'retrieve.php' calls the 'patients' and 'visits' database tables
//to populate form elements


// include function files for this application
require_once('util_fns.php');
//require_once('learn.php');
//require_once('teach_fns.php');

session_start();

$cxn=db_connect();
if(mysqli_connect_errno()) {
	echo "Error: Could not connect to database.";
	exit;
}

//PROCEED ONLY IF USER IS 'owner'
if($_SESSION['valid_user']!="owner") {
	exit;
	$cxn->close();
}

$id_key=$_POST['id_key'];

//populate text boxes and fields in 'search_form.php'
call_visit_db($id_key,$cxn);


//function call_visit_db uses parameter $id_in (corresponding to id tag in 'search_form.php'
//to pull data from visits and patients tables with the corresponding column names

function call_visit_db($id_in,$cxn_in) {
	
	//decide if the column is in 'patients' table or else in 'visits' table
	if($id_in=='family' || $id_in=='first' || $id_in=='mrn' || $id_in=='age' || $id_in=='gender' 
		|| $id_in=='dob' || $id_in=='physician') {
		
		$query_id_in="select ".$id_in." from patients";
		$results_id_in=$cxn_in->query($query_id_in);
		$row_id_in=$results_id_in->fetch_assoc();
		$id=$row_id_in[$id_in];
		
	} else {

		$query_id_in="select ".$id_in." from visits";
		$results_id_in=$cxn_in->query($query_id_in);
		$row_id_in=$results_id_in->fetch_assoc();
		$id=$row_id_in[$id_in];	
	}
	
	//echo is caught by responseText method of document object (ajax_functions.js)
	echo $id;
	
	//$cxn_in->free();
}



?>