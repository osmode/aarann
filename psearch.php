<?php

// include function files for this application
require_once('util_fns.php');
echo("<br><br>");
do_html_header("POE beta v. 1.0<br>Set search parameters");

session_start();
check_valid_user();

//PROCEED ONLY IF USER IS 'owner'
if($_SESSION['valid_user']=="owner") {
?>

<br/>
<form action="poe.php" method="post">
Select database to search:<br/>
<select name="database">
<option value="PubMed">PubMed</option>
</select><br/>
Enter disease name (formatted): <br/>
<input name="disease_formatted" type="text" size="40"/><br/>
Enter query (e.g. "coronary+AND+artery+AND+disease"): <br/>
<input name="query_input" type="text" size="40"/><br/>
Number of abstracts to retrieve (1 to 200): <br/>
<input name="abstract_num" type="text" size="40"/><br/>
Maximum age (in days) of publications: <br>
<input name="age" type="text" size="40"/><br/>
<input type="submit" name="submit" value="Submit"/>
</form>
<br><br>

<?php
}
// give menu of options
display_user_menu();
do_html_footer();
?>
