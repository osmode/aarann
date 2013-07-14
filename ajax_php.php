<?php

require_once('util_fns.php');
session_start();


$cxn=db_connect();
if(mysqli_connect_errno()) {
	echo "Error: Could not connect to database.";
	exit;
}

//PROCEED ONLY IF USER IS 'OWNER'
if($_SESSION['valid_user']!="owner") {
	exit;
	$cxn->close();
}

//sent from function print_checkboxes(system) in file ajax_functions.js

$system=$_POST['system'];


$constitutional = array( 'fever','chills','night sweats', 'fatigue','weight loss','weight gain','malaise');
$heent=array( 'vision changes','hearing changes','tinnitus','dry eyes','rhinitis','sore throat',
	'odynophagia','dysphagia','halitosis','masses','ulcers');
$neuro=array( 'syncope','presyncope','dizziness','vertigo','seizures','motor weakness','sensory changes','tremor','confusion',
	'memory problems','gait changes');
$psych=array('depression','suicidality','homicidality','anxiety','hallucinations','obsession',
	'compulsion','insomnia');
$cv=array('chest pain','chest discomfort','syncope','presyncope','dyspnea at rest','dyspnea on exertion',
	'palpitations','orthopnea','edema');
$respiratory=array('dyspnea','dry cough','productive cough','wheezing','hemoptysis','change in sputum');
$gi=array('abdominal pain','nausea','vomiting','heartburn','reflux','hematemesis','bloody stools',
	'melena','diarrhea','constipation','fecal incontinence');
$gu=array('dysuria','frequency','hesitancy', 'urinary incontinence','dribbling','anuria','flank pain','suprapubic pain',
	'urethral discharge','genital lesion','impotence');
$msk=array('weakness','aches','joint pain','back pain','neck pain','bone trauma','arthralgias');
$breast=array('breast tenderness','breast lump','skin changes','nipple changes','nipple discharge');
$derm=array('skin lesion','rash','itching','hives','changing mole','lymphadenopathy');
$endo=array('polyuria','polydipsia','hot flashes','virilization','diminished libido',
	'feeling cold','feeling warm','changes in appearance','excessive growth');
$heme=array('lymphadenopathy','excessive bleeding','easily bruised','blood clot',
	'anticoagulated');
$immune=array('seasonal allergies','environmental allergies','AIDS/HIV infection',
	'transplant recipient','other immunodeficiency','anaphylaxis','hives','itching');
$gyn=array('currently pregnant','vaginal bleeding','vaginal discharge','vaginal itching',
	'genital lesion','spotting','amenorrhea','menorrhagia','metrorrhagia','pelvic pain');

/**************************************************************/



//print checkboxes based on value of $system_in
if($system == 'constitutional') interactive_boxes('contitutional',$constitutional,$cxn);

elseif($system == 'heent') interactive_boxes('heent',$heent,$cxn);

elseif($system== 'neuro') interactive_boxes('neuro',$neuro,$cxn);

elseif($system== 'psych') interactive_boxes('psych',$psych,$cxn);

elseif($system== 'cv') interactive_boxes('cv',$cv,$cxn);

elseif($system== 'respiratory') interactive_boxes('respiratory',$respiratory,$cxn);

elseif($system== 'gi') interactive_boxes('gi',$gi,$cxn);
	
elseif($system== 'gu') interactive_boxes('gu',$gu,$cxn);

elseif($system== 'msk') interactive_boxes('msk',$msk,$cxn);

elseif($system== 'breast') interactive_boxes('breast',$breast,$cxn);

elseif($system== 'derm') interactive_boxes('derm',$derm,$cxn);

elseif($system== 'endo') interactive_boxes('endo',$endo,$cxn);

elseif($system== 'heme') interactive_boxes('heme',$heme,$cxn);

elseif($system== 'immune') interactive_boxes('immune',$immune,$cxn);

elseif($system== 'gyn') interactive_boxes('gyn',$gyn,$cxn);

//echos html tags for the checkboxes corresponding to the system that was clicked on
//and queries DB to check them as appropriate
//input: name of system ($name_in) and array containing symptoms ($array_in)

function interactive_boxes($name_in,$array_in,$cxn_in) {

	foreach($array_in as $symptom) {
	$query_saved_value="select value from ros where symptom='".$symptom."'";
	$result_saved_value=$cxn_in->query($query_saved_value);
	$row=$result_saved_value->fetch_assoc();
	$value=$row['value'];
	$stored[0][$symptom]=$value;
	
	//if checked
	if($stored[0][$symptom]==1) {
		echo "<input type=\"checkbox\" onClick=\"javascript:remember_check('".$name_in."','".$symptom."');\" 
			id=\"".$symptom."\" name=\"".$name_in."\" value=\"".$symptom."\" checked >";
		echo $symptom;
		//echo "value=".$stored[0][$symptom];
		echo "<br>";
	}
	else {
		echo "<input type=\"checkbox\" onClick=\"javascript:remember_check('".$name_in."','".$symptom."');\" 
			id=\"".$symptom."\" name=\"".$name_in."\" value=\"".$symptom."\" >";
		echo $symptom;
		//echo "value=".$stored[0][$symptom];
		echo "<br>";
		}		
		
	}
}





?>
