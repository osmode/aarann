<?php

// include function files for this application
require_once('util_fns.php');
require_once('learn.php');
require_once('teach_fns.php');


do_html_header(" ");
echo "<h1><span id=\"title1\">co</span><span id=\"title2\">Incide</span></h1>";

echo "<br><br>Tip: Use the Tab key to cycle through fields.<br>";
echo "Do not enter patient names or identifying information!<br>";


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

//initialize tables 'patients' and 'visits' (defined in 'teach_fns.php') if not exists
//init_tables($cxn_in);
	
?>

	
<form action="search.php" method="post" id="form1" name="form1">
<table><tr><td>
<b>Handle:</b></td><td>
 <input name="mrn" id="mrn" value="000001" size="20"/>
</td><tr><td>
<b>Age in years:</b></td><td>
<input name="age" id="age" size="2"/>&nbsp&nbsp<br>
</td><td>
<b>Gender:</b></td<td>
<select name="gender" id="gender"><option value="male">Male</option>
<option value="female">Female</option>
<option value="dni">Do not identify</option>
</td>
</select></tr><br><br>
<tr><td>
<b>Chief complaint:</b></td><td>
<input name="cc" id="cc" size="20"/></td><br><br>
</tr></table><br>
<b>HPI:</b><br>
<textarea name="hpi" id="hpi" rows="10" cols="50"></textarea><br><br>
<b>Review of systems:</b><br><br>

<TABLE BORDER="0" CELLPADDING="2">

<tr><td> 
<input type="button" id="constitutional" name="system" value="constitutional" 
	onClick="javascript:print_checkboxes('constitutional');"><br>
<input type="button" id="heent" name="system" value="heent" 
	onClick="javascript:print_checkboxes('heent');"><br>
<input type="button" id="neuro" name="system" value="neuro" 
	onClick="javascript:print_checkboxes('neuro');"><br>
<input type="button" id="psych" name="system" value="psych" 
	onClick="javascript:print_checkboxes('psych');"><br>
<input type="button" id="cv" name="system" value="cv" 
	onClick="javascript:print_checkboxes('cv');"><br>
<input type="button" id="resp" name="system" value="respiratory" 
	onClick="javascript:print_checkboxes('respiratory');"><br>
<input type="button" id="gi" name="system" value="gi" 
	onClick="javascript:print_checkboxes('gi');"><br>
<input type="button" id="gu" name="system" value="gu" 
	onClick="javascript:print_checkboxes('gu');"><br>
<input type="button" id="msk" name="system" value="msk" 
	onClick="javascript:print_checkboxes('msk');"><br>	
<input type="button" id="breast" name="system" value="breast" 
	onClick="javascript:print_checkboxes('breast');"><br>	
<input type="button" id="gyn" name="system" value="gyn" 
	onClick="javascript:print_checkboxes('gyn');"><br>	
<input type="button" id="derm" name="system" value="derm" 
	onClick="javascript:print_checkboxes('derm');"><br>	
<input type="button" id="heme" name="system" value="heme" 
	onClick="javascript:print_checkboxes('heme');"><br>
<input type="button" id="immune" name="system" value="immune" 
	onClick="javascript:print_checkboxes('immune');"><br>					
<input type="button" id="endo" name="system" value="endo" 
	onClick="javascript:print_checkboxes('endo');"><br>	
</td>
<td><div id="displayresults">&nbsp << select an organ system&nbsp</div></td>
</tr>

</table>

<br><br>

<b>Home medications:</b> <br>
<textarea name="home_meds" id="home_meds" rows="7" cols="50">
</textarea><br>

<b>Allergies:</b> <br>
<textarea name="allergies" id="allergies" rows="3" cols="50">
</textarea><br>

<b>Past medical history:</b> <br>
<textarea name="pmh" id="pmh" rows="7" cols="50">
</textarea><br>

<b>Past surgical history:</b><br>
<textarea name="psh" id="psh" rows="7" cols="50">
</textarea><br><br>

<b>Social history:</b><br>
<textarea name="social_history" id="social_history" rows="7" cols="50"></textarea><br><br>

<!--
<table>
<tr>
<td id="right">City:</td><td><input name="city" id="city" type="text" size="20"/></td>
</tr><tr>
<td>Occupation:</td><td><input name="occupation" id="occupation" type="text" size="20"/></td>
</tr><tr>
<td>Feels safe at home?</td><td>
<input type="radio" name="safe" value="oui">yes&nbsp&nbsp&nbsp</input>
<input type="radio" name="safe" value="no">no</input> 
</td>
</tr><tr>
<td>Other:</td><td><input name="shx_other" id="shx_other" type="text" size="20"/></td>
</tr>
</table>
<br>
Currently smoking?<br>
<input type="radio" name="smoke" id="smoke" value="yes">yes</input><br>
<input type="radio" name="smoke" id="smoke" value="no">no</input><br>
<input type="radio" name="smoke" id="smoke" value="other">smokeless tobacco</input><br>
Pack-years: <input name="packyears" id="packyears" type="text" size="2"/>
<br><br>

Recreational drugs:&nbsp<input name="recdrugs" id="recdrugs" type="text" size="20"/><br><br>

Sexually active with:<br>
<input type="radio" name="sexactivity" value="men">men<br>
<input type="radio" name="sexactivity" value="women">women&nbsp<br>
<input type="radio" name="sexactivity" value="both">both&nbsp<br>
<input type="radio" name="sexactivity" value="none">not sexually active&nbsp<br><br><br>
-->

<b>Family history:</b><br>
<textarea name="family_history" id="family_history" rows="3" cols="50">
</textarea><br><br><br>
<table><tr><td>
<b>Vital signs</b></td></tr>
<tr><td>
Heart rate: </td><td><input name="heart_rate" id="heart_rate" type="text" size="2"/></td>
<td>
Respiratory rate:</td>
<td><input name="resp_rate" id="resp_rate" type="text" size="2"/></td>
<td>Pulse ox:</td>
<td><input name="pulseox" id="pulseox" type="text" size="2"/></td>
</tr><tr><td>
Systolic BP:</td>
<td><input name="systolic" id="systolic" type="text" size="2"/></td>
<td>Diastolic BP:</td>
<td><input name="diastolic" type="text" id="diastolic" size="2"/></td>
</tr><td>EKG:</td><td><input name="ekg" id="ekg" size="10"/></td></tr></table>
<!--
<td class="right">Glucose:</td>
<td><input name="glucose" type="text" id="glucose" size="2"/></td>
--><br><br>

<b>Physical exam</b><br>
(separate positive findings with semicolons)<br>
<table>
<tr><td>General: </td><td><input name="gen_exam" type="text" id="gen_exam" size="20"/></td></tr>
<tr><td>HEENT: </td><td><input name="heent_exam" type="text" id="heent_exam" size="20"/></td></tr>
<tr><td>CV: </td><td> <input name="cv_exam" type="text" id="cv_exam" size="20"/></td></tr>
<tr><td>Pulm: </td><td> <input name="pulm_exam" type="text" id="pulm_exam" size="20"/></td></tr>
<tr><td>GI: </td><td>  <input name="gi_exam" type="text" id="gi_exam" size="20"/></td></tr>
<tr><td>GU:  </td><td> <input name="gu_exam" type="text" id="gu_exam" size="20"/></td></tr>
<tr><td>Neuro:  </td><td> <input name="neuro_exam" type="text" id="neuro_exam" size="20"/></td></tr>
<tr><td>Psych: </td><td>  <input name="psych_exam" type="text" id="psych_exam" size="20"/></td></tr>
<tr><td>MSK:  </td><td> <input name="msk_exam" type="text" id="msk_exam" size="20"/></td></tr>
<tr><td>Derm:  </td><td> <input name="derm_exam" type="text" id="derm_exam" size="20"/></td></tr>
</table>
<br><br>
<b>Labs</b><br>
<table>
<tr>
<td>Na: </td><td><input name="sodium" type="text" id="sodium" size="2"/></td>
<td class="right">K: </td><td><input name="potassium" id="potassium" type="text" size="2"/></td>
<td class="right">Cl: </td><td><input name="chloride" id="chloride" type="text" size="2"/></td>
<td class="right">Bicarb: </td><td><input name="bicarb" id="bicarb" type="text" size="2"/></td>
<td class="right">BUN: </td><td class="left"><input name="bun" id="bun" type="text" size="2"/></td>
<td class="left">Cr: </td><td class="left"><input name="creatinine" id="creatinine" type="text" size="2"/></td>
</tr>
<tr>
<td class="right">WBC: </td><td><input name="wbc" id="wbc" type="text" size="2"/></td>
<td class="right">Plt: </td><td><input name="plt" id="plt" type="text" size="2"/></td>
<td class="right">Hb: </td><td><input name="hb" id="hb" type="text" size="2"/></td>
<td class="right">HCT: </td><td><input name="hct" id="hct" type="text" size="2"/></td>
</tr>
<tr>
<td class="right">AST: </td><td><input name="ast" id="ast" type="text" size="2"/></td>
<td class="right">ALT: </td><td><input name="alt" id="alt" type="text" size="2"/></td>
<td class="right">Alk Phos: </td><td><input name="alkphos" id="alkphos" type="text" size="2"/></td>
<td class="right">Bili total: </td><td><input name="tbili" id="tbili" type="text" size="2"/></td>
<td class="right">Bili direct: </td><td><input name="dbili" id="dbili" type="text" size="2"/></td>
</tr>
<tr>
<td class="right">INR: </td><td><input name="inr" id="inr" type="text" size="2"/></td>
<td class="right">PT: </td><td><input name="pt" id="pt"  type="text" size="2"/></td>
<td class="right">PTT: </td><td><input name="ptt" id="ptt" type="text" size="2"/></td>
</tr>

</table>
<br><br>
<table><tr><td>
<b>Other labs</b> <br>
(pertinent positives only!)<br>
<textarea name="labs_positive" id="labs_positive" rows="3" cols="40"></textarea><br><br>
</td><td>
<b>Other labs </b><br>
(pertinent negatives only!)<br>
<textarea name="labs_negative" id="labs_negative" rows="3" cols="40"></textarea><br><br>
</td>
</tr>
</table>

<b>Radiology</b><br>
<textarea name="radiology_text" id="radiology_text" rows="7" cols="50">
</textarea><br><br>

<b>Pathology</b><br>
<textarea name="pathology_text" id="pathology_text" rows="7" cols="50">
</textarea><br><br><br>

<input type="submit" name="submit" id="go_button" value="Go"/>
<input type="button" name="populate" id="populate_button" onClick="javascript:populate_all();" 
	value="Populate"/>
<input type="button" name="save" id="save_button" onClick="javascript:save_all();" 
	value="Save"/>
</form>
<br><br><br>
<div id="save_tag"> </div>

<?php

// give menu of options
do_html_footer();
display_user_menu();

?>
