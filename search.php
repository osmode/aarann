<?php

// include function files for this application
require_once('util_fns.php');
require_once('learn.php');
require_once('teach_fns.php');

//define normal range of vitals and labs here as constants

do_html_header(" ");
echo "<h1><span id=\"title1\">med</span><span id=\"title2\">Wally</span></h1>";

session_start();
check_valid_user();

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
echo "<br><br><br>";

//define input variables from search_form.php
$family_history=$_POST['family_history'];
$family=$_POST['family'];
$first=$_POST['first'];
$month=$_POST['month'];
$date=$_POST['date'];
$year=$_POST['year'];
$gender=$_POST['gender'];
$age=$_POST['age'];
$cc=$_POST['cc'];
$hpi=$_POST['hpi'];
$system=$_POST['system'];
$home_meds=$_POST['home_meds'];
$allergies=$_POST['allergies'];
$pmh=$_POST['pmh'];
$psh=$_POST['psh'];
$city=$_POST['city'];
$occupation=$_POST['occupation'];
$smoke=$_POST['smoke'];
$recdrugs=$_POST['recdrugs'];
$sexactivity=$_POST['sexactivity'];
$allergies=$_POST['allergies'];
$heart_rate=$_POST['heart_rate'];
$resp_rate=$_POST['resp_rate'];
$systolic=$_POST['systolic'];
$diastolic=$_POST['diastolic'];
$pulseox=$_POST['pulseox'];
$glucose=$_POST['glucose'];
$ekg=$_POST['ekg'];
$gen_exam=$_POST['gen_exam'];
$heent_exam=$_POST['heent_exam'];
$cv_exam=$_POST['cv_exam'];
$pulm_exam=$_POST['pulm_exam'];
$gi_exam=$_POST['gi_exam'];
$gu_exam=$_POST['gu_exam'];
$neuro_exam=$_POST['neuro_exam'];
$psych_exam=$_POST['psych_exam'];
$msk_exam=$_POST['msk_exam'];
$derm_exam=$_POST['derm_exam'];
$sodium=$_POST['sodium'];
$potassium=$_POST['potassium'];
$chloride=$_POST['chloride'];
$bicarb=$_POST['bicarb'];
$bun=$_POST['bun'];
$creatinine=$_POST['creatinine'];
$wbc=$_POST['wbc'];
$plt=$_POST['plt'];
$hb=$_POST['hb'];
$hct=$_POST['hct'];
$ast=$_POST['ast'];
$alt=$_POST['alt'];
$alkphos=$_POST['alkphos'];
$tbili=$_POST['tbili'];
$dbili=$_POST['dbili'];
$inr=$_POST['inr'];
$pt=$_POST['pt'];
$ptt=$_POST['ptt'];
$radiology_text=$_POST['radiology_text'];
$pathology_text=$_POST['pathology_text'];
$ros='';

//$query_hp is the string that will be processed into lines
$hp_query='';

//query table 'ros' to determine which symptoms have a value of 1
$query_ros="select * from ros";
$results_ros=$cxn->query($query_ros);
$num_ros=$results_ros->num_rows;

for($i=0;$i<$num_ros;$i++) {
	
	$row_ros=$results_ros->fetch_assoc();
	$symptom=$row_ros['symptom'];
	$value=$row_ros['value'];
	
	//$ros is a string of positive symptoms separated by commas
	
	if($value == 1) {
		$num_positive++;
		
		$ros.=$symptom.", ";
	}
}


//format an H&P as a single string called $hp
//determine title if patient is greater
if($gender == 'male') 
	$salutation="Mr.";
elseif($gender == 'female')
	$salutation="Ms.";

//$saluation is Mr./Ms. + Last name
$salutation.=" ".$family;

//clear $hp

$hp="===============================<br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
$hp.="HISTORY<br>";
$hp.="===============================<br><br><br>";

$hp.="Chief complain: ".$cc."<br><br>";

//populate arrays with home meds, allergies, pmh, psh
/*
$meds_list=explode("\n",$home_meds);
$allergies_list=explode("\n",$allergies);
$pmh_list=explode("\n",$pmh);
$psh_list=explode("\n",$psh);
$recdrugs_list=explode(",",$recdrugs);
$famhistory_list=explode("\n",$family_history);
*/

//add name, age, gender
if(strlen($family) > 0) {
	$hp.=$salutation." is a ".$age."-year-old ";

	if($gender=='male')
		$hp.="man ";
	elseif($gender=='female')
		$hp.="woman ";

	//concat chief complaint
	$hp.= "presenting with a chief complaint of ".$cc.". ";
}

$hp_query=$cc;

//$openingline to reuse for the assessment and plan
$openingline=$hp;

//concat hpi (will change hpi textbox later to make it a series of input fields)
$hp.=$hpi." ";
$hp.="<br><br><br>";

//review of systems
if($num_positive > 0) {
$hp.="A review of 14 systems was positive for: ".$ros."<br><br>";
$hp.="The review of systems was otherwise negative.";
} else {
$hp.="A review of 14 systems was negative.";
}

$hp.="<br><br><br>";

$home_meds=nl2br($home_meds);
$allergies=nl2br($allergies);
$pmh=nl2br($pmh);
$psh=nl2br($psh);
$family_history=nl2br($family_history);

//meds, allergies
$hp.="Home medication:<br><br>";

if(strlen($home_meds) > 0)
	$hp.=$home_meds."<br<br>";
else
	$hp.="None.<br><br>";

$hp.="<br><br>";

$hp.="Allergies and intolerances:<br><br>";

if(strlen($allergies) > 0) 
	$hp.=$allergies."<br><br>";
else
	$hp.="None.<br><br>";

$hp.="Past medical history:<br><br>";
		
if(strlen($pmh) > 0) {
	$hp.=$pmh."<br><br>";
	$hp_query=$pmh." ".$ros;
}
else
	$hp.="No significant past medical history.<br><br>";

$hp.="Past surgical history:<br><br>";


if(strlen($psh) > 0) 
	$hp.=$psh."<br><br>";
else
	$hp.="No significant past surgical history.<br><br>";

$hp.="Social history:<br><br>";

$hp.=$salutation." lives in ".$city." and ";

if( stripos($occupation,'unemployed') || stripos($occupation,'disability') )
	$hp.="does not currently work. ";
elseif( stripos($occupation,'retired') )
	$hp.="is retired. ";
else
	$hp.="works as a ".$occupation." ";

if($smoke=='no' && ($packyear==0 || !$packyear) )
	$hp.=$salutation." has no tobacco smoking history. ";
elseif($smoke=='no' && ($packyear>=1) )
	$hp.=$salutation." does not currently smoke but has a ".$packyear." pack-year smoking history. ";
elseif($smoke=='yes')
	$hp.=$salutation." is a smoker and has a ".$packyear." pack-year smoking history. ";
elseif($smoke=='other')
	$hp.=$salutation." uses smokeless tobacco. ";

if($safe=='yes') $hp.=$salutation." feels safe at home. ";
elseif($safe=='no') $hp.=$salutation." does not feel safe it home. ";

if(strlen($sxh_other) > 0) $hp.=$shx_other." ";

$hp.="<br><br>Recreational drugs: ".$recdrugs."<br>";
$hp.="Sexually active with: ".$sexactivity."<br><br><br>";

$hp.="Family history:<br><br>";
$hp.=$family_history."<br><br><br>";

$hp.="<br><br>";
$hp.="===============================<br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
$hp.="PHYSICAL EXAMINATION<br>";
$hp.="===============================<br><br>";

//vital signs
$hp.="Heart rate: ".$heart_rate."    Respiratory rate: ".$resp_rate."    Blood pressure: ";
$hp.=$systolic."/".$diastolic."    Pulse ox: ".$pulseox."%"."<br>Blood glucose: ".$glucose;
$hp.="<br>&nbsp&nbspEKG: ".$ekg;

$hp.="<br><br>General: ".$gen_exam."<br><br>";
$hp.="HEENT: ".$heent_exam."<br><br>";
$hp.="Cardiovascular: ".$cv_exam."<br><br>";
$hp.="Pulmonary: ".$pulm_exam."<br><br>";
$hp.="GI: ".$gi_exam."<br><br>";

if(strlen($gu_exam)>1) 
	$hp.="GU: ".$gu_exam."<br><br>";

$hp.="Neuro: ".$neuro_exam."<br><br>";

if(strlen($psych_exam) > 1) 
	$hp.="Psych: ".$psych_exam."<br><br>";
if(strlen($msk_exam) > 1) 
	$hp.="Musculoskeletal: ".$msk_exam."<br><br>";
if(strlen($derm_exam) > 1) 
	$hp.="Dermatological: ".$derm_exam."<br><br>";


//labs
$hp.="<br><br>";
$hp.="===============================<br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
$hp.="LABORATORY<br>";
$hp.="===============================<br><br>";

$hp.="Na:&nbsp".$sodium."&nbsp&nbspCl:&nbsp".$chloride."&nbsp&nbspBUN:&nbsp".$bun."&nbsp&nbspGlucose:&nbsp".$glucose;
$hp.="<br>";
$hp.="K:&nbsp".$potassium."&nbsp&nbspBicarb:&nbsp".$bicarb."&nbsp&nbspCr:&nbsp".$creatinine."<br>";
$hp.="WBC:&nbsp".$wbc."&nbsp&nbspPlt:&nbsp".$plt."&nbsp&nbspHb:&nbsp".$hb."&nbsp&nbspHCT:&nbsp".$hct."%<br>&nbsp";

if(strlen($ast) > 0 )
	$hp.="AST: ".$ast."&nbsp";
if(strlen($alt) > 0 )
	$hp.="ALT: ".$alt."&nbsp";
if(strlen($alkphos) > 0 )
	$hp.="Alk Phos: ".$alkphos."&nbsp";
if(strlen($tbili) > 0 )
	$hp.="Total bilirubin: ".$tbili."&nbsp";
if(strlen($dbili) > 0 )
	$hp.="Direct bilirubin: ".$dbili."&nbsp";

$hp.="<br>";

if(strlen($pt) > 0 )
	$hp.="PT: ".$pt."&nbsp";
if(strlen($ptt) > 0 )
	$hp.="PTT: ".$ptt."&nbsp";
if(strlen($inr) > 0 )
	$hp.="INR: ".$inr."&nbsp";

$hp.="<br><br><br>";

//radiology
$hp.="===============================<br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
$hp.="RADIOLOGY<br>";
$hp.="===============================<br><br>";
$hp.=$radiology_text;

$hp.="<br><br>";

//pathology
$hp.="===============================<br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
$hp.="PATHOLOGY<br>";
$hp.="===============================<br><br>";
$hp.=$pathology_text;

$hp.="<br><br>";

//assessment and plan
$hp.="===============================<br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
$hp.="ASSESSMENT AND PLAN<br>";
$hp.="===============================<br><br>";

echo $hp;

//call ddx(text_in) function with $hp as argument (defined in 'learn.php')
//remember to attach "filler" to the history and physical text in  $hp 
//to prevent the line-learning algorithm from choking on gaps

$hp_query=$hp_query." filler ";
	
$sets=array('meds_table','objective_table','subjective_table','vitals_table','history_table','radiology_table');
	
try {

	//create database tables
	//creates three tables: 'input', 'input_lines', and 'FAMILYNAME_lines'
	
	if(!(Line6::db_tables('input',$family,$cxn)))
		throw new Exception("Function db_tables failed in search.php",1);
}
catch (Exception $e) {
	echo "Exception ".$e->getCode(). ": ".$e->getMessage()."<br>".
		"in ".$e->getFile(). "on line ".$e->getLine(). "<br>";
}
try {

//define diagnosis lines
if(!(learn_lines('input',$family, $sets, $hp_query, $cxn))) 
	throw new Exception("Function learn_lines failed in search.php",1);	
}
catch (Exception $f) {
	echo "Exception ".$e->getCode(). ": ".$e->getMessage()."<br>".
		"in ".$e->getFile(). "on line ".$e->getLine(). "<br>";
}	

echo "<br><br><br><br>";	  

/*
class Line6 {
	
	public function __construct($concept_type,$concept_name,$pt0,$pt1,$pt2,$pt3,$pt4,$pt5,$db_cxn) {
	
		//underscore compound words representing $concept_name for creation of table 
		//$concept_name_lines
		//e.g. 'septic arthritis' must become septic_arthritis_lines
		
		
		$underscored=cleanup_input($concept_name);
		
		//6-part string formed by concatenating the input points
		$name_string=$pt0.".".$pt1.".".$pt2.".".$pt3.".".$pt4.".".$pt5;		
		
		//echo "\$namestring=".$name_string."<br>";

	
		//insert the 6-part string into the $concept_type_lines table (eg. diagnosis_lines)
		$query_insert_line="insert into ".$concept_type."_lines(coord) values 
		('".$name_string."')";
		
		//insert the 6-part string into the $concept_name_lines table (eg. stroke_lines)		
		$query_new_concept="insert into ".$underscored."_lines(coord) values 
		('".$name_string."')";
		
		$db_cxn->query($query_insert_line);
		$db_cxn->query($query_new_concept);
				
		}

	static public function db_tables($concept_type,$concept_name,$db_cxn) {
	
		//underscore compound words representing $concept_name for creation of table 
		//$concept_name_lines
		//e.g. 'septic arthritis' must become septic_arthritis_lines
		
		$underscored=cleanup_input($concept_name);
		
		//create concept_type_linestable (eg. 'diagnosis_lines')
		// 'id' column is an auto-incremented line identifier
		// 'coord' is a string representing the line; used for searches
		// 'name' is the name of the $concept_name
		
		$query_type_lines_table="create table if not exists ".$concept_type."_lines (
			id int(10) unsigned not null primary key auto_increment,
			coord varchar(50) not null,
			name varchar(50) not null,
			unique (coord),
			fulltext (coord)
			) engine=myisam"; 
		
		//create concept_name_lines table (e.g. stroke_lines)
		// 'id' column is an auto-incremented line identifier
		// 'coord' si a string representing the line; used for searches
		
		$query_name_lines_table="create table if not exists ".$underscored."_lines (
			id int(10) unsigned not null primary key auto_increment,
			coord varchar(50) not null,
			unique (coord),
			fulltext (coord)
			) engine=myisam";
		
		//insert the $concept_name into the $concept_type table (eg. insert 'stroke'
		//into 'diagnosis'
		
		$query_type_table="create table if not exists ".$concept_type." (
			id int(10) unsigned not null primary key auto_increment,
			name varchar(50) not null,
			unique (name),
			fulltext (name)
			) engine=myisam";
			
		$query_insert_name="insert into ".$concept_type."(name) values ('".$concept_name."')";
		
		$db_cxn->query($query_type_lines_table);
		$db_cxn->query($query_name_lines_table);
		$db_cxn->query($query_type_table);
		$db_cxn->query($query_insert_name);
		
		return true;
		
	}

}


function learn_lines($concept_type, $concept_name, array $sets_in, $text_in, $cxn_in) {
	
	//REMEMBER TO CLEAN UP INPUT TEXT, REMOVE NON-LETTERS,make lowercase

	//$text_in=preg_replace("/[^a-zA-Z\s]/"," ",$text_in);		
	$text_in=strtolower(trim($text_in));
	
	//set counter variables to zero
	$num_sets=0;
	$num_filler=0;
	$m=0;
	$q=0;

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
	
			if( (stripos($text_in,$set[$x][$y]['name'])) !== false ) {
			
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

						if($num_filler<3) 
							$line[$q]=new Line6($concept_type,$concept_name,$temp[$f][0],$temp[$e][1],$temp[$d][2],$temp[$c][3],$temp[$b][4],$temp[$a][5],$cxn_in);
			
						$num_filler=0;
									
						echo "Inserting: ".$temp[$f][0].".".$temp[$e][1].".".$temp[$d][2].".".$temp[$c][3].".".$temp[$b][4].".".$temp[$a][5]."<br>";

						$q++;						
						
						}
					}
				}
			}
		}
	}
	} //close if($total<MAX_LINES)
	else echo "Too many diagnosis_lines. Please try again with a smaller entry.<br>";
	
	} //close elseif
	
	return true;
}

*/

	  
// give menu of options
do_html_footer();
display_user_menu();
?>
