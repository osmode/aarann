<?php
require_once('util_fns.php');
session_start();

//clean_input() defined in data_valid_fns.php
$db=$_POST['database'];
//$db=clean_input($db);

//disease name per mysql database entry
$disease_name=$_POST['disease_formatted'];
//$disease_name=clean_input($disease_name);

//what to query pubmed with (literal)
$query=$_POST['query_input']; 
//$query=clean_input($query);

//total number of documents to retrieve
$dnum = (is_numeric($_POST['abstract_num']) ? (int)$_POST['abstract_num'] : 0);
//$dnum=clean_input($dnum);

//age of query results in days
$term = (is_numeric($_POST['age']) ? (int)$_POST['age'] : 0);
//$term=clean_input($term);

// PubMED record ID's from e-search initialize to NULL
$pids = NULL;  
$rettype='abstract';
$retmode='text';
$retstart=0;

$base='http://eutils.ncbi.nlm.nih.gov/entrez/eutils/';
$url=$base."esearch.fcgi?db=$db&term=$query&usehistory=y";

//post esearch URL
$handle=fopen($url,"r");
$contents=fread($handle, 4096);

//get WebEnv and QueryKey using $contents
preg_match("/<WebEnv>(\S+)<\/WebEnv>/i",$contents,$match);
$web=$match[1];
preg_match("/<QueryKey>(\d+)<\/QueryKey>/i",$contents,$match);
$key=$match[1];

//assemble esummary URL
$url=$base."esummary.fcgi?db=$db&query_key=$key&WebEnv=$web";

$handle=fopen($url,"r");
$contents=fread($handle, 4096);

//assemble efetch URL
$url=$base."efetch.fcgi?db=$db&query_key=$key&WebEnv=$web";
$url .= "&rettype=abstract&retmode=text&retstart=$retstart&retmax=$dnum";

$handle=fopen($url,"r");
$contents=stream_get_contents($handle);
$contents=strip_tags($contents);

$d_points = "$disease_name";
$d_points .= "_points";

print $d_points;
print "<br><br>";
print $contents;
echo "<br><br>";

//connect to diseases database
$cxn=db_connect();

if(mysqli_connect_errno()) {
	echo "Error: Could not connect to database.";
	exit;
}
$placeholder="";

$words = explode(" ", $contents);
//$query5="insert into $d_points (select * from organs where match(name) 
//against ($placeholder in boolean mode))";


for ($i=0; $i<strlen($contents); $i++)
{
	$words[$i]=strtolower($words[$i]);
	
	$query_organs="insert into ".$d_points." (select * from organs where match(name) 
	against ('".$words[$i]."' in boolean mode))";

	$query_tissues="insert into ".$d_points." (select * from tissues where match(name) 
	against ('".$words[$i]."' in boolean mode))";

	$query_organ_systems="insert into ".$d_points." (select * from organ_systems 
	where match(name) against ('".$words[$i]."' in boolean mode))";
	
	$query_cells="insert into ".$d_points." (select * from cells where match(name) 
	against ('".$words[$i]."' in boolean mode))";

	$query_ss="insert into ".$d_points." (select * from ss where match(name) 
	against ('".$words[$i]."' in boolean mode))";

	$cxn->query($query_organ_systems);
	$cxn->query($query_organs);
	$cxn->query($query_tissues);
	$cxn->query($query_cells);
	$cxn->query($query_ss);

//make sure the dynamic SQL string is being read properly 
/*
	if($i>2 && $i<10) {
		echo $query5;
	echo $words[$i];
	echo "<br>";
	}
*/

}

fclose($handle);
fclose($url);

?>
