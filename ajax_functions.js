function getXMLHTTPRequest() {
	var req= false;
	try {
		/* for FireFrox */
		req=new XMLHttpRequest();
	} catch (err) {
		try {
		
			/* for some versions of IE */
			req= new ActiveXObject("Msxml2.XMLHTTP");
		} catch (err) {
			try {
				/* for some other versions of IE */
				req=new ActiveXObject("Microsoft.XMLHTTP");
			} catch (err) {
				req=false;
			}
		}
	}
	
	return req;
}

function print_checkboxes(system) {
	
	//open php file ajax_php
	var url="ajax_php.php";
	
	if(system == 'constitutional') {
		var params= 'system='+document.getElementById('constitutional').value;
	}
	else if(system== 'heent') {
		var params= 'system='+document.getElementById('heent').value;
	}
	else if(system== 'neuro') {
		var params= 'system='+document.getElementById('neuro').value;
	}
	else if(system== 'psych') {
		var params= 'system='+document.getElementById('psych').value;
	}
	else if(system== 'cv') {
		var params= 'system='+document.getElementById('cv').value;
	}
	else if(system== 'respiratory') {
		var params= 'system='+document.getElementById('resp').value;
	}
	else if(system== 'gi') {
		var params= 'system='+document.getElementById('gi').value;
	}
	else if(system== 'gu') {
		var params= 'system='+document.getElementById('gu').value;
	}
	else if(system== 'msk') {
		var params= 'system='+document.getElementById('msk').value;
	}
	else if(system== 'breast') {
		var params= 'system='+document.getElementById('breast').value;
	}
	else if(system== 'gyn') {
		var params= 'system='+document.getElementById('gyn').value;
	}
	else if(system== 'heent') {
		var params= 'system='+document.getElementById('heent').value;
	}
	else if(system== 'derm') {
		var params= 'system='+document.getElementById('derm').value;
	}
	else if(system== 'heme') {
		var params= 'system='+document.getElementById('heme').value;
	}
	else if(system== 'immune') {
		var params= 'system='+document.getElementById('immune').value;
	}
	else if(system== 'endo') {
		var params= 'system='+document.getElementById('endo').value;
	}

	myReq.open("POST","ajax_php.php",true);
	myReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	//myReq.setRequestHeader("Content-length",params.length);
	//myReq.setRequestHeader("Connection","close");
	myReq.onreadystatechange=addCheckboxResponse;
	myReq.send(params);

}

function addCheckboxResponse() {
	if(myReq.readyState==4) {
		if(myReq.status==200) {
			result=myReq.responseText;
			document.getElementById("displayresults").innerHTML=result;
		} else {
			alert('There was a problem with the request.');
		}
	}
}

function changeScreenSize(w,h) {
	window.resizeTo(w,h)
}

//this function is echoed in ajax_php.php and called in search_form.php
//input parameters: ROS system and a specific symptom
//this function sends the symptom info to ajax_php.php, where the DB is manipulated
//systems_in is used to defined all checkboxes with the name=systems_in

function remember_check(system_in,symptom_in) {
	
	//'system' is the $_POST key
	
	//determine true/false value of checkbox with id value="SYMPTOM NAME"
	//assign key='checkbox' for POST purposes
	check_status='checkbox='+document.getElementById(symptom_in).checked;
	
	//assign key to symptom_out
	symptom_out='symptom_checkbox='+symptom_in;
	
	//combine the two into a string, which will be explode()-ed in remember_form.php
	var param='string='+symptom_in+','+document.getElementById(symptom_in).checked;
	
	myReq2.open("POST","remember_form.php",true);
	myReq2.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	myReq2.onreadystatechange=symptom_response;
	myReq2.send(param);
	myReq2.setRequestHeader("Connection","close");

	
}

function symptom_response() {
	if(myReq2.readyState==4) {
		if(myReq2.status==200) {
			result=myReq2.responseText;
			document.getElementById("displayresults2").innerHTML=result;
		} else {
			alert('There was a problem with the request.');
		}
	}
}

//function suggest(tablename) dynamically makes suggestions based 
//on the lowest line of text in a textarea

function suggest(tablename) {

	//update only once per second to prevent browser
	//from choking
	
	var id_name= tablename+'_text';
	
	//alert(document.getElementById(meds_text).value);
	//id_name contains the textarea id name from teach_form.php
	//textarea contains the actual text
	//note that two pieces of information are sent in textarea:
	//the tablename and the textarea content, which are both
	//contained in textarea but separated by '***'
	var textarea="id_key="+tablename+"***"+document.getElementById(id_name).value;	
	//following request must be asynchronous (false) because of
	//the many consecutive open-send methods being called 
	suggestReq.open("POST","suggest.php",false);
	suggestReq.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	suggestReq.setRequestHeader("Content-length",textarea.length);
	suggestReq.setRequestHeader("Connection","close");
	suggestReq.onreadystatechange=suggest_receive(tablename);
	suggestReq.send(textarea);


}


//function suggest_receive receives database data from 'suggest.php'

function suggest_receive(param1) {
	
	return function() {
	if(suggestReq.readyState==4) {
		if(suggestReq.status==200) {
		
			result=suggestReq.responseText;
			//assigns the input field on search_form.php with id=id_tag 
			//the value returned from the db queries in 'suggest.php'
			
			if(result)
				//create proper div id
				var out_id=param1+"_suggest";
				document.getElementById(out_id).innerHTML=result;
				
		} else {
			alert('There was a problem with the populate_visit request');
		}
	}
	};
}


//function populate_visit_send posts data for purpose of filling in the table from the database
//id tags from search_form.php correspond to column names from tables 'patients' and 'visits'

function populate_visit_send(id_in) {
	
	//add key 'id_in' for $_POST purposes
	id_key='id_key='+id_in;

	//VERY IMPORTANT: this must be asynchronous ('false' below) because of the many
	//consecutive open-send methods being called in function populate_all()	
	popReq.open("POST","retrieve.php",false);
	popReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	popReq.setRequestHeader("Content-length",id_key.length);
	popReq.setRequestHeader("Connection","close");
		
	popReq.onreadystatechange=populate_visit_receive(id_in);	
	popReq.send(id_key);
	
}

function populate_all() {

populate_visit_send('mrn'); 
//populate_visit_send('dos'); 
populate_visit_send('family'); 
populate_visit_send('first');
populate_visit_send('age'); 
populate_visit_send('dob'); 
populate_visit_send('gender'); 
populate_visit_send('cc'); 
populate_visit_send('hpi'); 
populate_visit_send('home_meds'); 
populate_visit_send('allergies'); 
populate_visit_send('pmh'); 
populate_visit_send('psh'); 
populate_visit_send('city'); 
populate_visit_send('occupation'); 
populate_visit_send('safe'); 
populate_visit_send('shx_other'); 
populate_visit_send('smoke'); 
populate_visit_send('packyears'); 
populate_visit_send('recdrugs'); 
populate_visit_send('sexactivity'); 
populate_visit_send('family_history'); 
populate_visit_send('heart_rate'); 
populate_visit_send('resp_rate'); 
populate_visit_send('systolic'); 
populate_visit_send('diastolic');
populate_visit_send('pulseox'); 
populate_visit_send('glucose'); 
populate_visit_send('ekg'); 
populate_visit_send('gen_exam'); 
populate_visit_send('heent_exam'); 
populate_visit_send('cv_exam'); 
populate_visit_send('pulm_exam'); 
populate_visit_send('gi_exam'); 
populate_visit_send('gu_exam'); 
populate_visit_send('neuro_exam'); 
populate_visit_send('psych_exam'); 
populate_visit_send('msk_exam'); 
populate_visit_send('derm_exam'); 
populate_visit_send('sodium'); 
populate_visit_send('potassium');
populate_visit_send('chloride'); 
populate_visit_send('bicarb'); 
populate_visit_send('bun'); 
populate_visit_send('creatinine'); 
populate_visit_send('wbc'); 
populate_visit_send('plt'); 
populate_visit_send('hb'); 
populate_visit_send('hct'); 
populate_visit_send('ast'); 
populate_visit_send('alt'); 
populate_visit_send('alkphos'); 
populate_visit_send('tbili'); 
populate_visit_send('dbili'); 
populate_visit_send('inr'); 
populate_visit_send('pt'); 
populate_visit_send('ptt');
populate_visit_send('radiology_text'); 
populate_visit_send('pathology_text'); 
 
 
}

//function populate_visit_receive receives database data from 'ajax_php.php'
function populate_visit_receive(param1) {
	
	return function() {
	if(popReq.readyState==4) {
		if(popReq.status==200) {
		
			result=popReq.responseText;
			//assigns the input field on search_form.php with id=id_tag 
			//the value returned from the db queries in 'remember_form.php'
			
			if(result)
				document.getElementById(param1).value=result;
				
		} else {
			alert('There was a problem with the populate_visit request');
		}
	}
	};
}

//parameter column_in corresponds to a column in table visits or patients
//this function is used only for text-box input (not radio/checkboxes)

function save_form(column_in) {

//perform save only if textbox value is not null

	if(document.getElementById(column_in).value) {

	var id_key=column_in+'=';
	id_key+= document.getElementById(column_in).value;

	//VERY IMPORTANT: this must be asynchronous ('false' below) because of the many
	//consecutive open-send methods being called in function save_all()	
	saveReq.open("POST","save.php",false);
	saveReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	saveReq.setRequestHeader("Content-length",id_key.length);
	saveReq.setRequestHeader("Connection","close");
		
	saveReq.onreadystatechange=save_form_response;	
	saveReq.send(id_key);
	}

}

//only for textbox or textarea input
function save_form_response() {

	if(saveReq.readyState==4) {
		if(saveReq.status==200) {
		
			result=saveReq.responseText;
			document.getElementById("save_tag").innerHTML=result;
			
			//assigns the input field on search_form.php with id=id_tag 
			//the value returned from the db queries in 'remember_form.php'
					
		} else {
			alert('There was a problem with the populate_visit request');
		}
	}
}

function save_all() {

//mrn must come first

save_form('mrn');
save_form('family');
save_form('first');
save_form('age');
save_form('dob');
save_form('gender');
save_form('dos');
save_form('cc');
save_form('hpi');
save_form('home_meds');
save_form('allergies');
save_form('pmh');
save_form('psh');
save_form('city');
save_form('occupation');

which_radio_button('safe');
which_radio_button('smoke');
which_radio_button('sexactivity');

save_form('shx_other');
save_form('packyears');
save_form('recdrugs');
save_form('family_history');
save_form('heart_rate');
save_form('resp_rate');
save_form('systolic');
save_form('diastolic');
save_form('pulseox');
save_form('glucose');
save_form('ekg');
save_form('gen_exam');
save_form('heent_exam');
save_form('cv_exam');
save_form('pulm_exam');
save_form('gi_exam');
save_form('gu_exam');
save_form('neuro_exam');
save_form('psych_exam');
save_form('msk_exam');
save_form('derm_exam');
save_form('sodium');
save_form('potassium');
save_form('chloride');
save_form('bicarb');
save_form('bun');
save_form('creatinine');
save_form('wbc');
save_form('plt');
save_form('hb');
save_form('hct');
save_form('ast');
save_form('alt');
save_form('alkphos');
save_form('tbili');
save_form('dbili');
save_form('inr');
save_form('pt');
save_form('ptt');
save_form('radiology_text');
save_form('pathology_text');


}



//parameter: form_name is the NAME tag, not id, of the id buttons
function which_radio_button(form_name) {

	//perform only if a radio button (with a value) is checked
	
	if(document.getElementsByName(form_name)) {
	
		//get radio button values (determine which one is checked) 
		var radio=document.getElementsByName(form_name);

		for(var i=0,length=radio.length; i<length; i++) {
			if(radio[i].checked) {
				var check_value=radio[i].value;
			}
		}
		var id_key=form_name+'='+check_value;
	
		//VERY IMPORTANT: this must be asynchronous ('false' below) because of the many
		//consecutive open-send methods being called in function save_all()	
		saveReq.open("POST","save.php",false);
		saveReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		saveReq.setRequestHeader("Content-length",id_key.length);
		saveReq.setRequestHeader("Connection","close");
		
		saveReq.onreadystatechange=save_form_response;	
		saveReq.send(id_key);
	}

}

