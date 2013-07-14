<?php

// include function files for this application
require_once('util_fns.php');
do_html_header(" ");
echo "<h1><span id=\"title1\">co</span><span id=\"title2\">|ncident</span></h1>";

session_start();
check_valid_user();

$cxn=db_connect();

//PROCEED ONLY IF USER IS 'owner'
if($_SESSION['valid_user']!="owner") {
	exit;
	$cxn->close();
}

?>

<br>
<form action="teach.php" method="post">
<div class="bodytext">What would you like to do?:</div><br/>
<input type="radio" name="teach" value="entity">Teach medical vocabulary<br>
<input type="radio" name="teach" value="disease">Teach a disease process:&nbsp&nbsp
<input name="input_name" type="text" size="30"/><br>
<input type="radio" name="teach" value="diagnosis">Make a diagnosis<br>
<br><br>

<div class="bodytext">Enter a list of generic medications (separated by line breaks).</div><br>
<table> <tr><td> 
<textarea name="meds_text" id="meds_text" rows="4" cols="40" onkeyup="javascript:suggest('meds');"/>
</textarea></td>

<td>&nbsp&nbsp</td>
<td>
<div id="meds_suggest">&nbsp&nbsp&nbsp&nbsp</div>
</td>
</tr></table>

<br><br>
<div class="bodytext">Enter a list of laboratory findings findings and/or physical exam findings (separated by line breaks).</div><br>
<table> <tr><td> 
<textarea name="objective_text" id="objective_text" rows="4" cols="40" onkeyup="javascript:suggest('objective');"/>
</textarea></td>

<td>&nbsp&nbsp</td>
<td>
<div id="objective_suggest">&nbsp&nbsp&nbsp&nbsp</div>
</td>
</tr></table>

<div class="bodytext">Enter a list of symptoms (separated by line breaks).</div><br>
<table> <tr><td> 
<textarea name="subjective_text" id="subjective_text" rows="4" cols="40" onkeyup="javascript:suggest('subjective');"/>
</textarea></td>

<td>&nbsp&nbsp</td>
<td>
<div id="subjective_suggest">&nbsp&nbsp&nbsp&nbsp</div>
</td>
</tr></table>

<div class="bodytext">Enter a list of keywords for vitals and EKG (separated by line breaks).</div><br>
<table> <tr><td> 
<textarea name="vitals_text" id="vitals_text" rows="4" cols="40" onkeyup="javascript:suggest('vitals');"/>
</textarea></td>

<td>&nbsp&nbsp</td>
<td>
<div id="vitals_suggest">&nbsp&nbsp&nbsp&nbsp</div>
</td>
</tr></table>

<div class="bodytext">Enter a list of keywords from the patient's history (separated by line breaks).</div><br>
<table> <tr><td> 
<textarea name="history_text" id="history_text" rows="4" cols="40" onkeyup="javascript:suggest('history');"/>
</textarea></td>

<td>&nbsp&nbsp</td>
<td>
<div id="history_suggest">&nbsp&nbsp&nbsp&nbsp</div>
</td>
</tr></table>


<div class="bodytext">Enter a list of imaging/pathology keywords (separated by line breaks).</div><br>
<table> <tr><td> 
<textarea name="radiology_text" id="radiology_text" rows="4" cols="40" onkeyup="javascript:suggest('radiology');"/>
</textarea></td>

<td>&nbsp&nbsp</td>
<td>
<div id="radiology_suggest">&nbsp&nbsp&nbsp&nbsp</div>
</td>
</tr></table>
<br><br>

<div class="bodytext">Teach multiple disease process simultaneously. </div><br>

<textarea name="multiple_input" rows="10" cols="40">
</textarea><br><br>



<!--
Tell me about this disease: <br>
<textarea name="disease_text" rows="30" cols="75">
</textarea><br><br>
-->

<input type="submit" id="go_button" name="submit" value="Go"/>
</form>
<br><br>

<?php

// give menu of options
display_user_menu();
do_html_footer();
?>
