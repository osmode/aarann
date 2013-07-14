<?php
// learn.php: include file containing definitions of functions and classes 
// involved in learning nodes

// Textual input is broken down into words, which are declared as class Raw, input into
// a temporary table, and sifted through to determine and define members and declare
// each member as belonging to class Member. $node_name is used to output the resulting
// nodes into the appropriate database table
// the array $words[] is used to declare all the input words as belonging to class Raw

require_once('data_valid_fns.php');


function process_raw($node_in,$raw_in,$node_name,$cxn_in) {

	$counter=0;
	
	//create table 'raw_table' if not exists to enter each word of the input text
	//no unique keys to allow counting of entry frequency
	
	$query_raw_table="create temporary table raw_table (
			id int(10) unsigned not null primary key auto_increment,
			name varchar(50) not null
			)";
	$cxn_in->query($query_raw_table);
	
	
	echo "Node name: ".$node_name."<br>";
	//remove symbols from text input
	
	//isolate individual lowercase words without punctuation, white space or line breaks
	$sentences=explode(".",strip_tags($raw_in));
	$num_sentences=count($sentences);
	
	echo "Number of sentences: ".$num_sentences."<br><br>";
	
	//break each sentence down into words and insert them into raw_table
		
	foreach($sentences as $sent) {
		
		$words=explode(" ",$sent);
		$num_words=count($words);
		
		echo "Number of words in this sentence: ".$num_words."<br>";
		
		foreach($words as $word) {
			//echo $word."<br>";
		
			$raw[$counter]=new Raw($word,"raw_table",$cxn_in);
			
			$counter++;

		}
		
	}
	
	echo "Number of rows in raw_table: ".Raw::row_count(raw_table,$cxn_in)."<br>";
	
	//print out word frequencies 
	Raw::word_frequency(raw_table,$cxn_in);

	return true;

}

//each input word is defined as belonging to class Raw
//put each word in the temporary database table 'raw'
//and count how many of each word there are to help determine members

class Raw {

	//on declaration, insert row name into row_table
	public function __construct($word_in,$raw_tablename,$db_cxn) {
	
	//insert the word into table 'raw_table'
		$word_in=preg_replace("/[^a-zA-Z\s]/"," ",$word_in);		
		$word_in=strtolower(trim($word_in));
		$query_insert_raw="insert into ".$raw_tablename."(name) values ('".$word_in."')";
		$db_cxn->query($query_insert_raw);
				
	}

	//returns the number of rows in a table given input table name and connection
	static public function row_count($tablename,$db_cxn) {
		
		$query_select="select * from ".$tablename;
		$result_query_select=$db_cxn->query($query_select);
		$num_rows=$result_query_select->num_rows;
		
		return $num_rows;
	}
	
	//outputs how many times each word occurs and the percentage of total entries
	//input table must have the following format:
	//create temporary table raw_table (
	//	id int(10) unsigned not null primary key auto_increment,
	//	name varchar(50) not null );
	
	
	static public function word_frequency($tablename,$db_cxn) {
		
		$tab="&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
		$rowcount=Raw::row_count($tablename,$db_cxn);
		echo "Row".$tab."Word".$tab."Frequency".$tab."Percentage<br>";
		
		for($i=1;$i<($rowcount+1);$i++) {
		
			$query_word="select name from ".$tablename." where id=".$i;
			$result_query_word=$db_cxn->query($query_word);
			$query_name=$result_query_word->fetch_assoc();
			$name=$query_name['name'];
			$query_frequency="select * from ".$tablename." where name='".$name."'";
			$result_query_frequency=$db_cxn->query($query_frequency);
			$frequency=$result_query_frequency->num_rows;	
			$percentage= 100*($frequency/$rowcount);
			
			echo $i.$tab.$name.$tab.$frequency.$tab;
			printf ("%.3f", $percentage);
			echo "<br>";
			
			
		}
	}
	
	
}

//on declaration, enter into corresponding set database table 

class Member {
	
	public function __construct($word_in,$tablename,$level,$db_cxn) {
	
		//echo "Level: ".$level."<br>";
	
		//$word_in=preg_replace("/[^a-zA-Z.\s]/"," ",$word_in);		
		$word_in=strtolower(trim($word_in));
		
		//prevent empty database entries
		if(strlen($word_in) >1){
		$query_insert_member="insert into ".$tablename."(name,level) values 
			('".$word_in."',".$level.")";
		$db_cxn->query($query_insert_member);
		}

		}
}

//on declaration, create a new class Line
//input parameters for __construct: 

class Line6 {
	
	public function __construct($concept_type,$concept_name,$pt0,$pt1,$pt2,$pt3,$pt4,$pt5,$db_cxn) {
	
		//underscore compound words representing $concept_name for creation of table 
		//$concept_name_lines
		//e.g. 'septic arthritis' must become septic_arthritis_lines
		
		
		$underscored_name=cleanup_input($concept_name);
		$underscored_type=cleanup_input($concept_type);
		
		//6-part string formed by concatenating the input points
		$name_string=$pt0.".".$pt1.".".$pt2.".".$pt3.".".$pt4.".".$pt5;		
		
		//echo "\$namestring=".$name_string."<br>";

	
		//insert the 6-part string into the $concept_type_lines table (eg. diagnosis_lines)
		$query_insert_line="insert into ".$concept_type."_lines(coord,name) values 
		('".$name_string."','".$concept_name."')";
		
		//insert the 6-part string into the $concept_name_lines table (eg. stroke_lines)		
		/*
		$query_new_concept="insert into ".$underscored_name."_lines(coord) values 
		('".$name_string."')";
		*/

		$db_cxn->query($query_insert_line);
		$db_cxn->query($query_new_concept);
				
		}

	static public function db_tables($concept_type,$concept_name,$db_cxn) {
	
		//underscore compound words representing $concept_name for creation of table 
		//$concept_name_lines
		//e.g. 'septic arthritis' must become septic_arthritis_lines
		
		$underscored_name=cleanup_input($concept_name);
		$underscored_type=cleanup_input($concept_type);
		
		//create concept_type_linestable (eg. 'diagnosis_lines')
		// 'id' column is an auto-incremented line identifier
		// 'coord' is a string representing the line; used for searches
		// 'name' is the name of the $concept_name
		
		$query_type_lines_table="create table if not exists ".$underscored_type."_lines (
			id int(10) unsigned not null primary key auto_increment,
			coord varchar(50) not null,
			name varchar(50) not null,
			unique (coord),
			fulltext (coord)
			) engine=myisam"; 
		
		//create concept_name_lines table (e.g. stroke_lines)
		// 'id' column is an auto-incremented line identifier
		// 'coord' si a string representing the line; used for searches
		
		$query_name_lines_table="create table if not exists ".$underscored_name."_lines (
			id int(10) unsigned not null primary key auto_increment,
			coord varchar(50) not null,
			unique (coord),
			fulltext (coord)
			) engine=myisam";
		
		//insert the $concept_name into the $concept_type table (eg. insert 'stroke'
		//into 'diagnosis'
		
		$query_type_table="create table if not exists ".$underscored_type." (
			id int(10) unsigned not null primary key auto_increment,
			name varchar(50) not null,
			unique (name),
			fulltext (name)
			) engine=myisam";
			
		$query_insert_name="insert into ".$underscored_type."(name) values ('".$concept_name."')";
		
		$db_cxn->query($query_type_lines_table);
		$db_cxn->query($query_name_lines_table);
		$db_cxn->query($query_type_table);
		$db_cxn->query($query_insert_name);
		
		return true;
		
	}

}

/******************************************************/
// class input_line6 is used to define input lines 
/******************************************************/

class input_Line6 {
	
	public function __construct($concept_type,$concept_name,$pt0,$pt1,$pt2,$pt3,$pt4,$pt5,$db_cxn) {
	
		//underscore compound words representing $concept_name for creation of table 
		//$concept_name_lines
		//e.g. 'septic arthritis' must become septic_arthritis_lines
		
		$underscored_name=cleanup_input($concept_name);
		$underscored_type=cleanup_input($concept_type);
		
		//6-part string formed by concatenating the input points
		$name_string=$pt0.".".$pt1.".".$pt2.".".$pt3.".".$pt4.".".$pt5;		
		
		//insert the 6-part string into the $concept_type_lines table (eg. diagnosis_lines)
		$query_insert_line="insert into ".$concept_type."_lines(coord,name) values 
		('".$name_string."','".$concept_name."')";
		
		//insert the 6-part string into the $concept_name_lines table (eg. stroke_lines)		
		$query_new_concept="insert into ".$underscored_name."_lines(coord) values 
		('".$name_string."')";
		
		$db_cxn->query($query_insert_line);
		$db_cxn->query($query_new_concept);
				
		}
	
	//creates temporary tables for input lines
	static public function db_tables($concept_type,$concept_name,$db_cxn) {
	
		//underscore compound words representing $concept_name for creation of table 
		//$concept_name_lines
		//e.g. 'septic arthritis' must become septic_arthritis_lines
		
		$underscored=cleanup_input($concept_name);
		
		//create concept_type_linestable (eg. 'diagnosis_lines')
		// 'id' column is an auto-incremented line identifier
		// 'coord' is a string representing the line; used for searches
		// 'name' is the name of the $concept_name
		
		$query_type_lines_table="create temporary table if not exists ".$concept_type."_lines (
			id int(10) unsigned not null primary key auto_increment,
			coord varchar(50) not null,
			name varchar(50) not null,
			unique (coord),
			fulltext (coord)
			) engine=myisam"; 
		
		//create concept_name_lines table (e.g. stroke_lines)
		// 'id' column is an auto-incremented line identifier
		// 'coord' si a string representing the line; used for searches
		
		$query_name_lines_table="create temporary table if not exists ".$underscored."_lines (
			id int(10) unsigned not null primary key auto_increment,
			coord varchar(50) not null,
			unique (coord),
			fulltext (coord)
			) engine=myisam";
		
		//insert the $concept_name into the $concept_type table (eg. insert 'stroke'
		//into 'diagnosis'
		
		$query_type_table="create temporary table if not exists ".$concept_type." (
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

		
/****************************************************************************************/		
//function 'ddx()' 







?>
