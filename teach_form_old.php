<?php

// include function files for this application
require_once('util_fns.php');
echo("<br><br>");
do_html_header("Educate POE (Engine beta v. 1.0)");
echo "<br><br>";

session_start();
check_valid_user();

//PROCEED ONLY IF USER IS 'owner'
if($_SESSION['valid_user']!="owner") {
	exit;
	$cxn->close();
}

?>

<br>
<form action="teach.php" method="post">
What should I do?:<br/>
<select name="entity_type">
<option value="blank"> </option>
<option value="entity">Enter entity</option>
<option value="disease">Define disease</option>
<option value="therapeutic">Define therapeutic</option>
<option value="language">Define hierarchy</option>
</select><br><br>
Should I learn the language structure (Select "Define hierarchy" above)?
<br>
<input type="radio" name="linguist" value="hierarchy">Learn hierarchy<br>
<input type="radio" name="linguist" value="terms">Learn terminology<br>
<input type="radio" name="linguist" value="none">No<br><br>

Disease or therapy name: <br>
<input name="input_name" type="text" size="40"/><br>
<br>

Enter a list of subcellular entities here (separate by spaces). 
Add a comma after the last one.
<textarea name="ss_text" rows="5" cols="75">
</textarea><br><br>

Enter a list of cell types here (separate by spaces).<br>
<textarea name="cell_text" rows="5" cols="75">
</textarea><br><br>

Enter a list of tissues here (separate by spaces).<br>
<textarea name="tissue_text" rows="5" cols="75">
</textarea><br><br>

Enter a list of organs here (separate by spaces).<br>
<textarea name="organ_text" rows="5" cols="75">
</textarea><br><br>

Enter disease or therapeutic information here (if defining a disease or therapeutic). <br>
<textarea name="text2" rows="30" cols="75">
</textarea><br><br>

<br><br>
<input type="submit" name="submit" value="Go"/>
</form>
<br><br>

<?php

// give menu of options
display_user_menu();
do_html_footer();
?>
