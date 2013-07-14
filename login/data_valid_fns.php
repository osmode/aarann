<?php

function filled_out($form_vars) {
  // test that each variable has a value
  foreach ($form_vars as $key => $value) {
     if ((!isset($key)) || ($value == '')) {
        return false;
     }
  }
  return true;
}

function valid_email($address) {
  // check an email address is possibly valid
  if (ereg('^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$', $address)) {
    return true;
  } else {
    return false;
  }
}

function cleanup_input($user_input) {

	$user_input=trim($user_input);
	$user_input=addslashes($user_input);
	$user_input=strtolower($user_input);
	$pre_explode=$user_input;
	$exploded=explode(" ", $user_input);
	$number_words=count($exploded);
	
	if( count($exploded) > 1 ) {
		$formatted_word=$exploded[0];
		
		for($i=0;$i<$number_words;$i++) {
			if($i < ($number_words-1) ) 		
				$formatted_word=$formatted_word."_".$exploded[$i+1];
		}
		$formatted_word=trim($formatted_word);
	}
	else
		$formatted_word=trim($pre_explode);
		
	return $formatted_word;
}

?>
