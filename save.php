<?php
//'save.php' saves form data from search_form.php into the 'patients' and 'visits'
// database tables

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

$family=$_POST['family'];
$first=$_POST['first'];
$mrn=$_POST['mrn'];
$age=$_POST['age'];
$gender=$_POST['gender'];
$dob=$_POST['dob'];
//$physician=$_POST['physician'];
$mrn=$_POST['mrn'];
$dos=$_POST['dos'];
$cc=$_POST['cc'];
$hpi=$_POST['hpi'];
$home_meds=$_POST['home_meds'];
$allergies=$_POST['allergies'];
$pmh=$_POST['pmh'];
$psh=$_POST['psh'];
$city=$_POST['city'];
$occupation=$_POST['occupation'];
$safe=$_POST['safe'];
$shx_other=$_POST['shx_other'];
$smoke=$_POST['smoke'];
$packyears=$_POST['packyears'];
$recdrugs=$_POST['recdrugs'];
$sexactivity=$_POST['sexactivity'];
$family_history=$_POST['family_history'];
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

//mrn must come first and must equal '000001'
save_patient('mrn',$mrn,$cxn);
save_patient('family',$family,$cxn);
save_patient('first',$first,$cxn);
save_patient('dob',$dob,$cxn);
save_patient('age',$age,$cxn);
save_patient('dos',$dos,$cxn);
save_patient('gender',$gender,$cxn);

save_visit('cc',$cc,$cxn);
save_visit('hpi',$hpi,$cxn);
save_visit('home_meds',$home_meds,$cxn);
save_visit('allergies',$allergies,$cxn);
save_visit('pmh',$pmh,$cxn);
save_visit('psh',$psh,$cxn);
save_visit('safe',$safe,$cxn);
save_visit('smoke',$smoke,$cxn);
save_visit('packyears',$packyears,$cxn);
save_visit('sexactivity',$sexactivity,$cxn);
save_visit('city',$city,$cxn);
save_visit('occupation',$occupation,$cxn);
save_visit('shx_other',$shx_other,$cxn);
save_visit('recdrugs',$recdrugs,$cxn);
save_visit('family_history',$family_history,$cxn);
save_visit('heart_rate',$heart_rate,$cxn);
save_visit('resp_rate',$resp_rate,$cxn);
save_visit('systolic',$systolic,$cxn);
save_visit('diastolic',$diastolic,$cxn);
save_visit('pulseox',$pulseox,$cxn);
save_visit('glucose',$glucose,$cxn);
save_visit('ekg',$ekg,$cxn);
save_visit('gen_exam',$gen_exam,$cxn);
save_visit('heent_exam',$heent_exam,$cxn);
save_visit('cv_exam',$cv_exam,$cxn);
save_visit('pulm_exam',$pulm_exam,$cxn);
save_visit('gi_exam',$gi_exam,$cxn);
save_visit('gu_exam',$gu_exam,$cxn);
save_visit('neuro_exam',$neuro_exam,$cxn);
save_visit('psych_exam',$psych_exam,$cxn);
save_visit('msk_exam',$msk_exam,$cxn);
save_visit('derm_exam',$derm_exam,$cxn);
save_visit('sodium',$sodium,$cxn);
save_visit('potassium',$potassium,$cxn);
save_visit('chloride',$chloride,$cxn);
save_visit('bicarb',$bicarb,$cxn);
save_visit('bun',$bun,$cxn);
save_visit('creatinine',$creatinine,$cxn);
save_visit('wbc',$wbc,$cxn);
save_visit('plt',$plt,$cxn);
save_visit('hb',$hb,$cxn);
save_visit('hct',$hct,$cxn);
save_visit('ast',$ast,$cxn);
save_visit('alt',$alt,$cxn);
save_visit('alkphos',$alkphos,$cxn);
save_visit('tbili',$tbili,$cxn);
save_visit('dbili',$dbili,$cxn);
save_visit('inr',$inr,$cxn);
save_visit('pt',$pt,$cxn);
save_visit('ptt',$ptt,$cxn);
save_visit('radiology_text',$radiology_text,$cxn);
save_visit('pathology_text',$pathology_text,$cxn);



//parameters: column name (in table 'patients') and $form_string[] element

function save_patient($column_in,$value_in,$cxn_in) {

	//update table only with non-null values
	if($value_in) {
	$query_save_form="update patients set ".$column_in."='".$value_in."' where mrn='000001'"; 
	$cxn_in->query($query_save_form);
}


}

//parameters: column name (in table 'visits') and $form_string[] element
function save_visit($column_in,$value_in,$cxn_in) {

	//update database with non-null values
	if($value_in) {
		$query_save_form="update visits set ".$column_in."='".$value_in."' where mrn=000001"; 
		$cxn_in->query($query_save_form);
	}

}


?>
