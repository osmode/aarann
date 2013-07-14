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


//used for storing checkbox values in a database
//$string_in[0]: symptom; $string_in[1]: checked value (true or false)
$string_in=$_POST['string'];
$string_in=explode(",",$string_in);

/*******************************/
//		ROS entries
/*******************************/

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
/*********************************/

	//echo "symptom_checkbox: ".$string_in2[0]."<br>";
	//echo "checkbox: ".$string_in2[1];
	
//create ros table if not exists
$query_ros_table="create table if not exists ros (
	symptom varchar(50) not null,
	value tinyint not null,
	unique (symptom)
	) engine=myisam";
	
//populate table ros with symptoms
foreach($constitutional as $symptom) {
	$query_populate_ros="insert into ros(symptom,value) values ('".$symptom."',0)";
	$cxn->query($query_populate_ros);
}
foreach($heent as $symptom) {
	$query_populate_ros="insert into ros(symptom,value) values ('".$symptom."',0)";
	$cxn->query($query_populate_ros);
}
foreach($neuro as $symptom) {
	$query_populate_ros="insert into ros(symptom,value) values ('".$symptom."',0)";
	$cxn->query($query_populate_ros);
}
foreach($psych as $symptom) {
	$query_populate_ros="insert into ros(symptom,value) values ('".$symptom."',0)";
	$cxn->query($query_populate_ros);
}
foreach($cv as $symptom) {
	$query_populate_ros="insert into ros(symptom,value) values ('".$symptom."',0)";
	$cxn->query($query_populate_ros);
}
foreach($respiratory as $symptom) {
	$query_populate_ros="insert into ros(symptom,value) values ('".$symptom."',0)";
	$cxn->query($query_populate_ros);
}
foreach($gi as $symptom) {
	$query_populate_ros="insert into ros(symptom,value) values ('".$symptom."',0)";
	$cxn->query($query_populate_ros);
}
foreach($gu as $symptom) {
	$query_populate_ros="insert into ros(symptom,value) values ('".$symptom."',0)";
	$cxn->query($query_populate_ros);
}
foreach($msk as $symptom) {
	$query_populate_ros="insert into ros(symptom,value) values ('".$symptom."',0)";
	$cxn->query($query_populate_ros);
}
foreach($breast as $symptom) {
	$query_populate_ros="insert into ros(symptom,value) values ('".$symptom."',0)";
	$cxn->query($query_populate_ros);
}
foreach($derm as $symptom) {
	$query_populate_ros="insert into ros(symptom,value) values ('".$symptom."',0)";
	$cxn->query($query_populate_ros);
}
foreach($endo as $symptom) {
	$query_populate_ros="insert into ros(symptom,value) values ('".$symptom."',0)";
	$cxn->query($query_populate_ros);
}
foreach($heme as $symptom) {
	$query_populate_ros="insert into ros(symptom,value) values ('".$symptom."',0)";
	$cxn->query($query_populate_ros);
}
foreach($immune as $symptom) {
	$query_populate_ros="insert into ros(symptom,value) values ('".$symptom."',0)";
	$cxn->query($query_populate_ros);
}
foreach($gyn as $symptom) {
	$query_populate_ros="insert into ros(symptom,value) values ('".$symptom."',0)";
	$cxn->query($query_populate_ros);
}


//add symptom and checkbox value to table ros
$query_update="update ros set value=".$string_in[1]." where symptom='".$string_in[0]."'";

$cxn->query($query_ros_table);
$cxn->query($query_update);


?>
