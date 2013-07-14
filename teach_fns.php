<?php
require_once('data_valid_fns.php');
require_once('learn.php');

//maximum number of lines to be drawn for input and diagnosis lines
define("MAX_LINES",5000);

define("NUM_SETS",6);

//max number of fillers permitted in a line (i.e. number of fillers must be less than these)
define("MAX_FILLERS",3);
define("MAX_INPUT_FILLERS",6);

/****************************************************************************************/
//FUNCTION learn_vocab enters input into one of five sets (database tables)
//by declaring class 'Member' (defined in 'learn.php' include file)

function learn_vocab($meds_in,$objective_in,$subjective_in,$vitals_in,$history_in,$radiology_in,$cxn_in)
{

//create TABLEs if they don't exist: meds_table, objective_table, subjective_table, 
//vitals_table, history_table, and radiology_table
//each of these sets contains important keywords used to define diseases as permutations 
//of members from each set

$query_create_meds_table="create table if not exists meds_table (
	id int(10) unsigned not null primary key auto_increment,
	name varchar(50) not null,
	level int(10) unsigned not null default 0,
	fulltext (name),
	unique (name)
	) engine=myisam";
$query_create_objective_table="create table if not exists objective_table (
	id int(10) unsigned not null primary key auto_increment,
	name varchar(50) not null,
	level int(10) unsigned not null default 1,
	fulltext (name),
	unique (name)
	) engine=myisam";
$query_create_subjective_table="create table if not exists subjective_table (
	id int(10) unsigned not null primary key auto_increment,
	name varchar(50) not null,
	level int(10) unsigned not null default 2,
	fulltext (name),
	unique (name)
	) engine=myisam";
$query_create_vitals_table="create table if not exists vitals_table (
	id int(10) unsigned not null primary key auto_increment,
	name varchar(50) not null,
	level int(10) unsigned not null default 3,
	fulltext (name),
	unique (name)
	) engine=myisam";
$query_create_history_table="create table if not exists history_table (
	id int(10) unsigned not null primary key auto_increment,
	name varchar(50) not null,
	level int(10) unsigned not null default 4,
	fulltext (name),
	unique (name)
	) engine=myisam";
$query_create_radiology_table="create table if not exists radiology_table (
	id int(10) unsigned not null primary key auto_increment,
	name varchar(50) not null,
	level int(10) unsigned not null default 5,
	fulltext (name),
	unique (name)
	) engine=myisam";
	
	$cxn_in->query($query_create_meds_table);
	$cxn_in->query($query_create_objective_table);
	$cxn_in->query($query_create_subjective_table);
	$cxn_in->query($query_create_vitals_table);
	$cxn_in->query($query_create_history_table);
	$cxn_in->query($query_create_radiology_table);

//clean up input: explode, trim, do word count

$meds_words=explode("\n",$meds_in);
$num_meds=count($meds_words);
$objective_words=explode("\n",$objective_in);
$num_objective=count($objective_words);
$subjective_words=explode("\n",$subjective_in);
$num_subjective=count($subjective_words);
$vitals_words=explode("\n",$vitals_in);
$num_vitals=count($vitals_words);
$history_words=explode("\n",$history_in);
$num_history=count($history_words);
$radiology_words=explode("\n",$radiology_in);
$num_radiology=count($radiology_words);

//total number of words
$total=$num_meds+$num_objective+$num_subjective+$num_vitals+$num_history+$num_radiology;
$counter=0;

//define hierarchy level for each set
$level_meds="0";
$level_objective="1";
$level_subjective="2";
$level_vitals="3";
$level_history="4";
$level_radiology="5";

//if at least one medication is entered, trim it down and put it in TABLE meds_table
if($num_meds > 0) {
		
	echo "Number of medications: ".$num_meds;
	echo "<br>";
		
	foreach($meds_words as $med) { 
				
		$member[$counter]=new Member($med,"meds_table",$level_meds,$cxn_in);
		$counter++;
		
				
	}
}
if($num_objective > 0) {
		
	echo "Number of objective: ".$num_objective;
	echo "<br>";
		
	foreach($objective_words as $objective) { 
		
		$member[$counter]=new Member($objective,"objective_table",$level_objective,$cxn_in);
		$counter++;
		
	}
}
if($num_subjective > 0) {
		
	echo "Number of subjective: ".$num_subjective;
	echo "<br>";
		
	foreach($subjective_words as $subjective) { 
	
		$member[$counter]=new Member($subjective,"subjective_table",$level_subjective,$cxn_in);
		$counter++;
		
	}
}
if($num_vitals > 0) {
		
	echo "Number of vitals: ".$num_vitals;
	echo "<br>";
		
	foreach($vitals_words as $vitals) { 
		
		$member[$counter]=new Member($vitals,"vitals_table",3,$cxn_in);
		$counter++;
		
	}
}		
if($num_history > 0) {
		
	echo "Number of history: ".$num_history;
	echo "<br>";
		
	foreach($history_words as $history) { 

		$member[$counter]=new Member($history,"history_table",$level_history,$cxn_in);
		$counter++;		
		
	}
}
if($num_radiology > 0) {
		
	echo "Number of radiology: ".$num_radiology;
	echo "<br>";
		
	foreach($radiology_words as $radiology) { 
		
		$member[$counter]=new Member($radiology,"radiology_table",$level_radiology,$cxn_in);
		$counter++;
		
	}
}

		return true;
		
}

//end FUNCTION learn_vocab
/****************************************************************************************/
// definition of function learn_lines, to be differentiated from function input_learn_lines
// learn_lines creates a (permanent) tables and runs the stripos() function against
// 6 separate textareas
//parameter array $text_in contains the contents of the 6 textareas from teach_form.php
/****************************************************************************************/

function learn_lines($concept_type, $concept_name, array $sets_in, array $text_in, $cxn_in) {
	
	//REMEMBER TO CLEAN UP INPUT TEXT, REMOVE NON-LETTERS,make lowercase
	
	for($i=0;$i<NUM_SETS;$i++) 
		$text_in[$i]=strtolower(trim($text_in[$i]));
		
	//$text_in=preg_replace("/[^a-zA-Z\s]/"," ",$text_in);		
	
	//set counter variables to zero
	$num_sets=0;
	$num_filler=0;
	$m=0;
	$q=0;
	$lines_counter=0;
	
	//create line segments (must have at least 2 sets)
	$num_sets=count($sets_in);
	
	echo "Number of sets: ".$num_sets."<br>";
	
	if($num_sets==1) {
		echo "Number of sets must be at least 2.";
		return false;
	}
	elseif($num_sets>1) {

	//create three-dimensional array named $set[x][y][z] where layer x is an entire
	//database table (a set), y is a row in the table, and z is the column
	//each column has the key 'id', 'name', or 'level'
	
	//for each input set $x...
	for($x=0;$x<$num_sets;$x++) {
			
		//echo names of database tables containing the sets	
		//echo $sets_in[$x];
		
		//count the number of rows in each input set
		$query_num_rows="select * from ".$sets_in[$x];
		$results_num_rows=$cxn_in->query($query_num_rows);
		$num_rows=$results_num_rows->num_rows;

		//echo "Number of rows: ".$num_rows."<br>";

		//$set_num_rows[$x] is a one-dimensional array holding the number of rows
		//in the x-th input set
		$set_num_rows[$x]=$num_rows;

		for($y=0;$y<$num_rows;$y++) {
		
			$row=$results_num_rows->fetch_assoc();
			$set[$x][$y]['id']=$row['id'];
			$set[$x][$y]['name']=$row['name'];
			$set[$x][$y]['level']=$row['level'];
			
			//echo "\$set[".$x."][".$y."]"."['id']= ".$set[$x][$y]['id'];
			//echo "<br>";
			//echo "\$set[".$x."][".$y."]"."['name']= ".$set[$x][$y]['name'];
			//echo "<br>";
			//echo "\$set[".$x."][".$y."]"."['level']= ".$set[$x][$y]['level'];
			//echo "<br>";
		}
	}

	//for each input set $x...
	for($x=0;$x<$num_sets;$x++) {
	
		//for each row $y...
		for($y=0;$y<$set_num_rows[$x];$y++) {
			
		//check each 'name' of each $set for a match against the input text
		//and add corresponding match's ID values from $sets matches to a two-dimensional 
		//array called temp[m][n] where each row m represents a matching 'name' entry
		//and each column n corresponds to an input set ($x)
	
			if( (stripos($text_in[$x],$set[$x][$y]['name'])) !== false ) {
			
				echo "Match found: ".$set[$x][$y]['name']."<br>";
				$temp[$m][$x]=$set[$x][$y]['id'];
	
				//$m counts how many matches were found
				$m++;
				
			}
			
		}
		
		//$num_matches keeps track of how many matches were found
		$num_matches[$x]=$m;
		//echo "Number of matches in set number ".$x.": ".$num_matches[$x];
		//echo "<br>";
		$m=0;
	}
	
	$total_matches=$num_matches[0]+$num_matches[1]+$num_matches[2]+$num_matches[3]+$num_matches[4]+$num_matches[5];
	$total=($num_matches[0]-1)*($num_matches[1]-1)*($num_matches[2]-1)*($num_matches[3]-1)*($num_matches[4]-1)*($num_matches[5]-1);
	
	echo "Number of matches: ".$total_matches."<br>";
	
	//"snake" through each set from bottom to top (level 0 upward) to represent
	//all permutations as a "line"
	//each set must contain a "filler" element to prevent gaps in the snaking
	//algorithm caused by empty sets
	//$num_sets=6 here
	//THE FOLLOWING SERIES OF NESTED LOOPS MUST BE SET UP MANUALLY BASED ON
	//THE NUMBER OF INPUT SETS TO PREVENT USE OF RECURSIVE STRUCTURES
		
	//proceed only if the total number of matches is not too great
	if($total<MAX_LINES) {
	for($f=0;$f<$num_matches[$num_sets-6];$f++) {
	
		for($e=0;$e<$num_matches[$num_sets-5];$e++) {
		
			for($d=0;$d<$num_matches[$num_sets-4];$d++) {
				
				for($c=0;$c<$num_matches[$num_sets-3];$c++) {
				
					for($b=0;$b<$num_matches[$num_sets-2];$b++) {
						
						for($a=0;$a<$num_matches[$num_sets-1];$a++) {
						
						//learn lines only if they have less than 3 "filler" points
						if($temp[$f][0]<2) $num_filler++;
						if($temp[$e][1]<2) $num_filler++;
						if($temp[$d][2]<2) $num_filler++;
						if($temp[$c][3]<2) $num_filler++;
						if($temp[$b][4]<2) $num_filler++;
						if($temp[$a][5]<2) $num_filler++;
						
//declares a new 6-point line as belonging to class Line6
//first parameter is the type of concept (used to create a database table)
//"type of concept"_lines as well as another table "type of concept" (entry: $concept_name)		

	
						//if($num_filler<MAX_FILLERS) {
							$line[$q]=new Line6($concept_type,$concept_name,$temp[$f][0],$temp[$e][1],$temp[$d][2],$temp[$c][3],$temp[$b][4],$temp[$a][5],$cxn_in);
							echo "Inserting: ".$temp[$f][0].".".$temp[$e][1].".".$temp[$d][2].".".$temp[$c][3].".".$temp[$b][4].".".$temp[$a][5]."<br>";
							
							$lines_counter++;
						//}
			
						//$num_filler=0;

						$q++;						
						
						}
					}
				}
			}
		}
	}
	
	if($lines_counter==0) echo "Too many fillers. MAX_FILLERS = ".MAX_FILLERS."<br>";

	} //close if($total<MAX_LINES)
	else echo "Too many diagnosis_lines. Please try again with a smaller entry.<br>";
	
	} //close elseif
	
	return true;
}

/****************************************************************************************/
// definition of function input_learn_lines, to be differentiated from function learn_lines
// input_learn_lines creates temporary tables and runs the stripos() function against
// 6 separate textareas
//parameter array $text_in contains the contents of the 6 textareas from teach_form.php
/****************************************************************************************/

function input_learn_lines($concept_type, $concept_name, array $sets_in, array $text_in, $cxn_in) {
	
	//REMEMBER TO CLEAN UP INPUT TEXT, REMOVE NON-LETTERS,make lowercase

	//$text_in=preg_replace("/[^a-zA-Z\s]/"," ",$text_in);		
	for($i=0;$i<NUM_SETS;$i++) 
		$text_in[$i]=strtolower(trim($text_in[$i]));
	
	//set counter variables to zero
	$num_sets=0;
	$num_filler=0;
	
	//$m keeps track of number of matches found
	$m=0;
	$q=0;
	$lines_counter=0;

	//create line segments (must have at least 2 sets)
	foreach($sets_in as $tablename)
		$num_sets++;
	
	echo "Number of sets: ".$num_sets."<br>";
	
	if($num_sets==1) {
		echo "Number of sets must be at least 2.";
		return false;
	}
	elseif($num_sets>1) {

	//create three-dimensional array named $set[x][y][z] where layer x is an entire
	//database table (a set), y is a row in the table, and z is the column
	//each column has the key 'id', 'name', or 'level'
	
	//for each input set $x...
	for($x=0;$x<$num_sets;$x++) {
			
		//echo names of database tables containing the sets	
		//echo $sets_in[$x];
		
		//count the number of rows in each input set
		$query_num_rows="select * from ".$sets_in[$x];
		$results_num_rows=$cxn_in->query($query_num_rows);
		$num_rows=$results_num_rows->num_rows;

		//echo "Number of rows: ".$num_rows."<br>";

		//$set_num_rows[$x] is a one-dimensional array holding the number of rows
		//in the x-th input set
		$set_num_rows[$x]=$num_rows;

		for($y=0;$y<$num_rows;$y++) {
		
			$row=$results_num_rows->fetch_assoc();
			$set[$x][$y]['id']=$row['id'];
			$set[$x][$y]['name']=$row['name'];
			$set[$x][$y]['level']=$row['level'];
			
			//echo "\$set[".$x."][".$y."]"."['id']= ".$set[$x][$y]['id'];
			//echo "<br>";
			//echo "\$set[".$x."][".$y."]"."['name']= ".$set[$x][$y]['name'];
			//echo "<br>";
			//echo "\$set[".$x."][".$y."]"."['level']= ".$set[$x][$y]['level'];
			//echo "<br>";
		}
	}

	//for each input set $x...
	for($x=0;$x<$num_sets;$x++) {
	
		//for each row $y...
		for($y=0;$y<$set_num_rows[$x];$y++) {
			
		//check each 'name' of each $set for a match against the input text
		//and add corresponding match's ID values from $sets matches to a two-dimensional 
		//array called temp[m][n] where each row m represents a matching 'name' entry
		//and each column n corresponds to an input set ($x)
	
			if( (stripos($text_in[$x],$set[$x][$y]['name'])) !== false ) {
			
				echo "Match found: ".$set[$x][$y]['name']."<br>";
				$temp[$m][$x]=$set[$x][$y]['id'];
	
				//$m counts how many matches were found
				$m++;
				
			}
			
		}
		
		//$num_matches keeps track of how many matches were found
		$num_matches[$x]=$m;
		echo "Number of matches in set number ".$x.": ".$num_matches[$x];
		echo "<br>";
		$m=0;
	}
	
	$total_matches=$num_matches[0]+$num_matches[1]+$num_matches[2]+$num_matches[3]+$num_matches[4]+$num_matches[5];
	$total=($num_matches[0]-1)*($num_matches[1]-1)*($num_matches[2]-1)*($num_matches[3]-1)*($num_matches[4]-1)*($num_matches[5]-1);
	
	echo "Number of matches: ".$total_matches."<br>";
	
	//"snake" through each set from bottom to top (level 0 upward) to represent
	//all permutations as a "line"
	//each set must contain a "filler" element to prevent gaps in the snaking
	//algorithm caused by empty sets
	//$num_sets=6 here
	//THE FOLLOWING SERIES OF NESTED LOOPS MUST BE SET UP MANUALLY BASED ON
	//THE NUMBER OF INPUT SETS TO PREVENT USE OF RECURSIVE STRUCTURES
		
	//proceed only if the total number of matches is not too great
	if($total<MAX_LINES) {
	for($f=0;$f<$num_matches[$num_sets-6];$f++) {
	
		for($e=0;$e<$num_matches[$num_sets-5];$e++) {
		
			for($d=0;$d<$num_matches[$num_sets-4];$d++) {
				
				for($c=0;$c<$num_matches[$num_sets-3];$c++) {
				
					for($b=0;$b<$num_matches[$num_sets-2];$b++) {
						
						for($a=0;$a<$num_matches[$num_sets-1];$a++) {
						
						//learn lines only if they have less than 3 "filler" points
						if($temp[$f][0]<2) $num_filler++;
						if($temp[$e][1]<2) $num_filler++;
						if($temp[$d][2]<2) $num_filler++;
						if($temp[$c][3]<2) $num_filler++;
						if($temp[$b][4]<2) $num_filler++;
						if($temp[$a][5]<2) $num_filler++;
						
//declares a new 6-point line as belonging to class Line6
//first parameter is the type of concept (used to create a database table)
//"type of concept"_lines as well as another table "type of concept" (entry: $concept_name)		

						
						if($num_filler<MAX_INPUT_FILLERS) {
							$line[$q]=new input_Line6($concept_type,$concept_name,$temp[$f][0],$temp[$e][1],$temp[$d][2],$temp[$c][3],$temp[$b][4],$temp[$a][5],$cxn_in);
							echo "Inserting: ".$temp[$f][0].".".$temp[$e][1].".".$temp[$d][2].".".$temp[$c][3].".".$temp[$b][4].".".$temp[$a][5]."<br>";

							$lines_counter++;

						}
			
						$num_filler=0;
									
			
						$q++;						
						
						}
					}
				}
			}
		}
	}
	if($lines_counter==0) echo "Too many fillers. MAX_INPUT_FILLERS = ".MAX_INPUT_FILLERS."<br>";
	
	} //close if($total<MAX_LINES)
	else echo "Too many diagnosis_lines. Please try again with a smaller entry.<br>";
	
	} //close elseif
	
	return true;
}



/****************************************************/

function enter_entity($ss_in,$cell_in,$tissue_in,$organ_in,$cxn_in)
{

//EXPLODE and TRIM each input textarea into words and do word count
$ss_words=explode("\n",$ss_in);
$num_ss=count($ss_words);
$cell_words=explode("\n",$cell_in);
$num_cell=count($cell_words);
$tissue_words=explode("\n",$tissue_in);
$num_tissue=count($tissue_words);
$organ_words=explode("\n",$organ_in);
$num_organ=count($organ_words);

//enter entities into master tables
if($num_ss>0) {

	for($i=0;$i<($num_ss);$i++) {
		$ss_words[$i]=trim($ss_words[$i]);
		$query_insert_ss="insert into ss2(name) values ('".$ss_words[$i]."')";
		$cxn_in->query($query_insert_ss);

	}
}
if($num_cell>0) {
	
	echo $num_cell;
	echo "<br>";
	
	for($j=0;$j<($num_cell);$j++) {
	
		$cell_words[$j]=trim($cell_words[$j]);
		$query_insert_cell="insert into cells2(name) values ('".$cell_words[$j]."')";
		$cxn_in->query($query_insert_cell);

	}
}
if($num_tissue>0) {
	echo $num_tissue;
	echo "<br>";
	
	for($k=0;$k<($num_tissue);$k++) {
		$tissue_words[$k]=trim($tissue_words[$k]);	
		$query_insert_tissue="insert into tissues2(name) values ('".$tissue_words[$k]."')";
		$cxn_in->query($query_insert_tissue);

	}
}
if($num_organ>0) {
	
	for($l=0;$l<($num_organ);$l++) {
		$organ_words[$l]=trim($organ_words[$l]);
		$query_insert_organ="insert into organs2(name) values ('".$organ_words[$l]."')";
		$cxn_in->query($query_insert_organ);

	}
}
		
		return true;
		
}

//initializes patients and visits tables if not exists
function init_tables($cxn_in) {

//create fake patient database
$query_patients="create table if not exists patients (
	family varchar(50) not null,
	first varchar(50) not null,
	mrn int(10) unsigned not null,
	age tinyint unsigned not null,
	gender varchar(50) not null,
	dob date null,
	physician tinytext null
)";

//create fake_patient table to store values
$query_visit="create table if not exists visits (
	mrn int(10) unsigned not null,
	cc tinytext null,
	hpi tinytext null,
	home_meds tinytext null,
	allergies tinytext null,
	pmh tinytext null,
	psh tinytext null,
	city varchar(50) null,
	occupation varchar(50) null,
	safe varchar(50) null,
	shx_other tinytext null,
	smoke varchar(50) null,
	packyears tinyint null,
	recdrugs tinytext null,
	sexactivity varchar(50) null,
	family_history tinytext null,
	heart_rate tinyint unsigned null,
	resp_rate tinyint unsigned null,
	systolic tinyint unsigned null,
	diastolic tinyint unsigned null,
	pulseox tinyint unsigned null,
	glucose tinyint unsigned null,
	ekg tinytext null,
	gen varchar(50) null,
	heent varchar(50) null,
	cv varchar(50) null,
	pulm varchar(50) null,
	gi varchar(50) null,
	gu varchar(50) null,
	neuro varchar(50) null,
	psych varchar(50) null,
	msk varchar(50) null,
	derm varchar(50) null,
	sodium tinyint null,
	potassium tinyint null,
	chloride tinyint null,
	bicarb tinyint null,
	bun tinyint null,
	creatinine tinyint null,
	wbc tinyint null,
	plt tinyint null,
	hb tinyint null,
	hct tinyint null,
	ast tinyint null,
	alt tinyint null,
	alkphos tinyint null,
	tbili tinyint null,
	dbili tinyint null,
	inr tinyint null,
	pt tinyint null,
	ptt tinyint null,
	radiology_text tinytext null,
	pathology_text tinytext null
	)";

$cxn_in->query($query_patients);
$cxn_in->query($query_visits);

}



?>
