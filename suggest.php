<?php

// 'suggest.php' is called by 'suggest(tablename)' in 
// 'ajax_functions.js', receives a one-dimensional array 
// containing a tablename and textarea contents, and echos
// out suggestions from the corresponding database table

require_once('util_fns.php');

session_start();

$cxn=db_connect();
if(mysqli_connect_errno()) {
	echo "Error: Could not connect to database.";
	exit;
}

$id_key=$_POST['id_key'];

//extract table name and text from $id_key

$exploded_id_key=explode("***",$id_key);
$tablename=$exploded_id_key[0];
$tablename=trim($tablename)."_table";
$textarea=$exploded_id_key[1];


//obtain the text on the lowest line of $textarea because
//this will be used to query the db for suggestions
$lines=explode("\n",$textarea);
$num_lines=count($lines);
$last_line=$lines[$num_lines-1];
//$last_line=strtolower(trim($last_line));

$query_suggestions="select name from ".$tablename." where name like '".$last_line."%' limit 7";

$result_suggestions=$cxn->query($query_suggestions);
$num_suggestions=$result_suggestions->num_rows;
$outstring="";

for($i=0;$i<$num_suggestions;$i++) {
	$row=$result_suggestions->fetch_assoc();
	$outstring.=$row['name'];
	$outstring.="<br>";
}

//tell user if no matches
if($num_suggestions==0) $outstring="No matches";


//at the very end, echo a single string containing all 
//suggestions

echo $outstring;
//echo $id_key;

//$cxn->free();
 


?>
