<?php
require_once('util_fns.php');
require_once('teach_fns.php');
require_once('data_valid_fns.php');
require_once('learn.php');

//number of sets (in this case, 6)
define("NUM_SETS",6);
define("DDX_RESULTS",5);

session_start();
do_html_header('');
echo "<br>";
echo "<br>";

check_valid_user();

// give menu of options
display_user_menu();
do_html_footer();

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

/***** Define POST variables *****/

$teach_type=$_POST['teach'];
$meds=$_POST['meds_text'];
$objective=$_POST['objective_text'];
$subjective=$_POST['subjective_text'];
$vitals=$_POST['vitals_text'];
$history=$_POST['history_text'];
$radiology=$_POST['radiology_text'];
$entity_name=$_POST['input_name'];
$text2=$_POST['disease_text'];
$multiple=$_POST['multiple_input'];

$sets=array('meds_table','objective_table','subjective_table','vitals_table','history_table','radiology_table');

//define array $text containing contents of the 6 text areas from teach_form.php
$text[0]=$meds;
$text[1]=$objective;
$text[2]=$subjective;
$text[3]=$vitals;
$text[4]=$history;
$text[5]=$radiology;		

//echo "Multiple input: ".strlen($multiple)."<br>";

//append " filler" to the input text to prevent gaps in the line-learning algorithm
for($i=0;$i<NUM_SETS;$i++)
	$text[$i].="\nfiller ";

//define medical terminology via learn_vocab (defined in teach_fns.php)
//input will be cleaned up in the function 
if($teach_type == "entity") {
	
	learn_vocab($meds,$objective,$subjective,$vitals,$history,$radiology,$cxn);
	$cxn->close();

}
elseif ($teach_type == "disease" ) {

	
	//if multiple_input textarea has text in it, run the process_multiple_inputs function
	if(strlen($multiple) > 1) {
		echo "Start multiple diagnosis entry...<br>";
		process_multiple_inputs($multiple, $sets, $cxn);
	}
	//...otherwise enter each diagnosis separately
	else {	

	//append " filler" to the input text to prevent gaps in the line-learning algorithm
	for($i=0;$i<NUM_SETS;$i++)
		$text[$i].="\nfiller ";

	//populate the db tables with new set members
	update_sets($sets,$text,NUM_SETS,$cxn);
		
	try {
	
	//create database tables
		if(!(Line6::db_tables('diagnosis',$entity_name,$cxn)))
			throw new Exception("Function db_tables failed",1);
	}
	catch (Exception $e) {
		echo "Exception ".$e->getCode(). ": ".$e->getMessage()."<br>".
			"in ".$e->getFile(). "on line ".$e->getLine(). "<br>";
	}
	try {
	//define diagnosis lines
	if(!(learn_lines('diagnosis',$entity_name, $sets, $text, $cxn))) 
		throw new Exception("Function learn_lines failed",1);	
	}
	catch (Exception $f) {
		echo "Exception ".$e->getCode(). ": ".$e->getMessage()."<br>".
			"in ".$e->getFile(). "on line ".$e->getLine(). "<br>";
	}
	
	echo "Learning disease process: ".$entity_name."<br>";
	
	}

	$cxn->close();	

} 

elseif ($teach_type == "diagnosis") {
	
	//populate the db tables with new set members
	//update_sets($sets,$text,NUM_SETS,$cxn);
	
	//append " filler" to the input text to prevent gaps in the line-learning algorithm
	//only if the textarea is blank
	
	for($i=0;$i<NUM_SETS;$i++) {
		if(strlen($text[$i])==0)
			$text[$i].="\nfiller ";	
	}
	
	try {
	
	//create database tables
		if(!(input_Line6::db_tables('input','temp1',$cxn)))
			throw new Exception("Function db_tables failed",1);
	}
	catch (Exception $e) {
		echo "Exception ".$e->getCode(). ": ".$e->getMessage()."<br>".
			"in ".$e->getFile(). "on line ".$e->getLine(). "<br>";
	}
	try {
	
	//define input lines
	if(!(input_learn_lines('input','temp1', $sets, $text, $cxn))) 
		throw new Exception("Function input_learn_lines failed",1);	
	}
	catch (Exception $f) {
		echo "Exception ".$e->getCode(). ": ".$e->getMessage()."<br>".
			"in ".$e->getFile(). "on line ".$e->getLine(). "<br>";
	}
	
	//search each of the input strings against the diagnosis_lines coords
	//to generate a differential diagnosis
	
	echo "<br>Differential diagnosis:<br><br>";
	ddx('temp1',$cxn);
	
	$cxn->close();		
	
}

/*************************************************************************/
// function ddx generates a differential diagnosis using a temporary table filled with coords
// and the diagnosis_lines table
// parameter: table_name (e.g. 'input' becomes 'input_lines')
// parameter: cxn_in (db connection handle)
/*************************************************************************/

function ddx($table_name,$cxn_in) {

//fetch each row from the temporary table and assign a coords value
//insert matchs' diagnosis names into an array
//then insert array contents (diagnosis names) into a temporary table
//so that each result can be counted

//create temporary table 'temp_matches' to store matches
$query_create_temp_matches="create temporary table if not exists temp_matches (
	id int(10) unsigned not null primary key auto_increment,
	coord varchar(50) not null,
	name varchar(50) not null,
	unique (coord)
	) engine=myisam";

//create temporary table 'temp_matches2' to count matches for the ddx
$query_create_temp_matches2="create temporary table if not exists temp_matches2 (
	name varchar(50) not null,
	count int(10) unsigned not null,
	unique (name)
	) engine=myisam";

	
$cxn_in->query($query_create_temp_matches);
$cxn_in->query($query_create_temp_matches2);


//query temporary table 'input_lines'
$query_temp_coords="select * from ".$table_name."_lines";
$result_temp_coords=$cxn_in->query($query_temp_coords);
$num_rows=$result_temp_coords->num_rows;

//query diagnosis_lines table and store as an array
$query_diagnosis_lines="select * from diagnosis_lines";
$result_diagnosis_lines=$cxn_in->query($query_diagnosis_lines);
$num_diagnosis_lines=$result_diagnosis_lines->num_rows;

//$num_matches counts how many diagnosis_lines were matches
$num_matches=0;
//reset counter
$q=0;

//store results in an array '$diagnosis_lines_arrray[x][y]


for($i=0;$i<$num_diagnosis_lines;$i++) {
	
	$rows_diagnosis_lines=$result_diagnosis_lines->fetch_assoc();
	$tempcoord=$rows_diagnosis_lines['coord'];
	$tempname=$rows_diagnosis_lines['name'];

	$diagnosis_lines_array[$i]['coord']=$tempcoord;
	$diagnosis_lines_array[$i]['name']=$tempname;
}

//cycle through each coord in the temporary table looking for matches against the 
// 'diagnosis_lines' table

for($i=0;$i<$num_rows;$i++) {
	
	$rows=$result_temp_coords->fetch_assoc();
	$current_coord=$rows['coord'];
	
	//for each row in the temporary table, check all diagnosis_lines (in the array)
	
	for($j=0;$j<$num_diagnosis_lines;$j++) {
		 
		//if the current temporary coord matches against the diagnosis_lines coord, add it
		//to another temporary table
		
		//echo "Comparing ".$diagnosis_lines_array[$j]['coord']." with ".$current_coord."<br>";
		
		if(stripos($diagnosis_lines_array[$j]['coord'],$current_coord) !== false){
			
			$query_add_coord="insert into temp_matches(coord,name) values ('".$current_coord."','".$diagnosis_lines_array[$j]['name']."')";
			$cxn_in->query($query_add_coord);
			
			//temp_matches_array is a two-dimensional array used to count the number of matches
			
			//$temp_matches_array[$j]['coord']=$current_coord;
			$temp_matches_array[$num_matches]['name']=$diagnosis_lines_array[$j]['name'];
			
			//echo "\$diagnosis_lines_array=".$diagnosis_lines_array[$j]['name']."<br>";
			//echo "match name: ".$temp_matches_array[$num_matches]['name']."<br>";
			
			$num_matches++;
		}
	}
}

//echo "Number of coord matches against diagnosis_lines: ".$num_matches."<br>";

for($i=0;$i<$num_matches;$i++) {

	//here is the problem: this query is not working...

	$query_count="select count(name) as count from temp_matches where name='".$temp_matches_array[$i]['name']."'";
	$result_count=$cxn_in->query($query_count);
	$rows_count=$result_count->fetch_assoc();
	$count=$rows_count['count'];
	
	//now insert the name and count(name) into another temporary table, order by count desc
	$query_populate_temp_matches2="insert into temp_matches2(name,count) values ('".$temp_matches_array[$i]['name']."',".$count.")";
	$cxn_in->query($query_populate_temp_matches2);
	
	//echo "Number of ".$temp_matches_array[$i]['name'].": ".$count."<br>";
	
}



//sort temp_matches2 by descending count and decide which of the matching names has the most 'hits'

$query_order="select * from temp_matches2 order by count desc";
$result_order=$cxn_in->query($query_order);
$num_order=$result_order->num_rows;

if($num_order==0) echo "Unable to generate a differential diagnosis. Please give me more information.<br>";
elseif($num_order>DDX_RESULTS) {
	for($i=0;$i<DDX_RESULTS;$i++) {
		
		$rows_order=$result_order->fetch_assoc();
		$name=$rows_order['name'];
		echo $i." ".$name."<br>";
	}
}
else {
	for($i=0;$i<$num_order;$i++) {
		
		$rows_order=$result_order->fetch_assoc();
		$name=$rows_order['name'];
		echo ($i+1).". ".$name."<br>";
	}
}
/*
$result_count->free();	
$result_order->free();
$result_temp_coords->free();
$result_diagnosis_lines->free();
*/

}


/*************************************************************************/
// function update_sets inputs each of the 6 text area contents into
// the corresponding database tables
// parameters: array $text_in (contains textarea contents separated by \n
// and array $sets_in contains the DB table names
// and $num_sets_in specifies the number of sets_in elements == text_in elements
// and $cxn_in is the db connection handle
/*************************************************************************/
function update_sets($sets_in,$text_in,$num_sets_in,$cxn_in) {

$q=0;

	for($i=0;$i<$num_sets_in;$i++) {
		$to_input=explode("\n",$text_in[$i]);
		$num_exploded=count($to_input);
		for($j=0;$j<$num_exploded;$j++) {
			
			//to prevent empty database entries
			//public function __construct($word_in,$tablename,$level,$db_cxn) 
				
			$member[$q]=new Member($to_input[$j],$sets_in[$i],$i,$cxn_in);				
				
			$q++;

		}
	}		
		
}


/*************************************************************************/
// function process_multiple_inputs extracts 6 text areas from a formatted block of test
// and is used for defining multiple diagnoses simultaneously
// parameters: array $sets_in contains the array with each element representing an input field
// parameters: array $text_in is the formatted text block defining multiple diagnoses
/*************************************************************************/
function process_multiple_inputs($text_in, array $sets_in, $cxn_in) {

	//first explode the text to separate disease process (spacer: '++++')
	//then explode to get the diagnosis name and the set definitions (spacer: '----')
	
	//text is an array with elements of diagnosis definitions 
	$input=explode("++++",$text_in);
	$num_dx=count($input);
	
	echo $num_dx." separate diagnoses entered.<br>";

	
	for($i=0;$i<$num_dx;$i++) {
		
		//fields is an array with elements of lists of diagnosis name, meds, subjective, etc
		
		$fields=explode("----",$input[$i]);
		$num_fields=count($fields);
				
		$entity_name=trim($fields[0]);

		echo "entity name: ".$entity_name."<br>";		
		
		//meds textarea
		$text[0]=$fields[1];

		//objective textarea
		$text[1]=$fields[2];
		
		//subjective textarea
		$text[2]=$fields[3];
		
		//vitals textarea
		$text[3]=$fields[4];
		
		//history textarea
		$text[4]=$fields[5];
		
		//radiology textarea
		$text[5]=$fields[6];
		
		echo "meds:<br>".$text[0]."<br><br>";
		echo "objective:<br>".$text[1]."<br><br>";
		echo "subjective:<br>".$text[2]."<br><br>";
		echo "vitals:<br>".$text[3]."<br><br>";
		echo "history:<br>".$text[4]."<br><br>";
		echo "radiology:<br>".$text[5]."<br><br>";

		//populate the db tables with new set members
		update_sets($sets_in,$text,NUM_SETS,$cxn_in);

		if(strlen($entity_name) > 1) {
		try {
	
		//create database tables
			if(!(Line6::db_tables('diagnosis',$entity_name,$cxn_in)))
				throw new Exception("Function db_tables failed",1);
		}
		catch (Exception $e) {
			echo "Exception ".$e->getCode(). ": ".$e->getMessage()."<br>".
				"in ".$e->getFile(). "on line ".$e->getLine(). "<br>";
		}
		try {
		//define diagnosis lines
		if(!(learn_lines('diagnosis',$entity_name, $sets_in, $text, $cxn_in))) 
			throw new Exception("Function learn_lines failed",1);	
		}
		catch (Exception $f) {
			echo "Exception ".$e->getCode(). ": ".$e->getMessage()."<br>".
				"in ".$e->getFile(). "on line ".$e->getLine(). "<br>";
		}
	
		echo "Learning disease process: ".$entity_name."<br>";
		
		}
		
		
	} //close for($i=0;$i<$num_dx;$i++)
	
}

?>
