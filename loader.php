<?php

require_once("includes/config.inc.php");
require_once("includes/connect.php");
require_once("includes/mysql_functions.php");
require_once("class.xmlparser.php");
require_once("class.search_results.php");

$db_id = $_GET['db_id'];
$search_terms = $_GET['search_terms'];
$parent_id = $_GET['parent_id'];
if(!strcmp($parent_id,''))
{
	// create a parent_id
	$fixed_search_terms = str_replace("'","\'",$search_terms);
	$fields = array('search_terms','vendor_dbs');
	$values = array($fixed_search_terms,$db_id);
	$parent_id = insert('search_cache',$fields,$values);
}

$select_dbs = "select * from vendor_dbs where ss_id like '$db_id'";
$res_dbs = mysql_query($select_dbs);
$db_obj = mysql_fetch_object($res_dbs);
$db_name = $db_obj->name;


// vendor result
$sr = new search_results($search_terms,$db_id);
//$sr->search_terms = $search_terms;
$vsr = new vendor_result();
$sr->vendor_results[] = $vsr;
//$vsr->vendor_id = $db_id;
//$vsr->vendor_name = "vendor name";

//$vsr->add_vendor_results();
//exit();

if(strcmp($db_id,'') && strcmp($search_terms,''))
{
	if(strcmp($db_obj->custom_loader,''))
	{
		switch($db_obj->custom_loader)
		{
			case 'search_google_scholar':
				require_once("google_scholar.php");
				$sr = search_google_scholar($db_id,$search_terms,$sr);
				break;
			case 'search_academic_search_premier':
				require_once("academic_search_premier.php");
				$sr = search_academic_search_premier($db_id,$search_terms,$sr);
				break;
			case 'search_agricola':
				require_once("agricola.php");
				$sr = search_agricola($db_id,$search_terms,$sr);
				break;
			case 'search_business_source_premier':
				require_once("business_source_premier.php");
				$sr = search_business_source_premier($db_id,$search_terms,$sr);
				break;
			case 'search_computer_source':
				require_once("computer_source.php");
				$sr = search_computer_source($db_id,$search_terms,$sr);
				break;
			case 'search_alt_healthwatch':
				require_once("alt_healthwatch.php");
				$sr = search_alt_healthwatch($db_id,$search_terms,$sr);
				break;
			case 'search_america_history_and_life':
				require_once("america_history_and_life.php");
				$sr = search_america_history_and_life($db_id,$search_terms,$sr);
				break;
			case 'search_avery_index':
				require_once("avery_index.php");
				$sr = search_avery_index($db_id,$search_terms,$sr);
				break;
			case 'search_bibliography_of_native_north_americans':
				require_once("bibliography_of_native_north_americans.php");
				$sr = search_bibliography_of_native_north_americans($db_id,$search_terms,$sr);
				break;
			
		}
	}
	else
	{
		require_once("multisearch.php");
		$sr = search_multisearch($db_id,$search_terms,$sr);
	}
	
	//$_GET['debug'] = 1;
	if(!strcmp($_GET['debug'],'1'))
	{
		print("<pre>\n");
		print_r($sr);
		print("</pre>\n");
	}
	$sr->save_result($parent_id);
	$total_results = $sr->vendor_results[0]->total_results;
	print("$db_name|$db_id|$total_results");

}
else
{

	$select = "select * from vendor_dbs where active like '1' order by name";
	//print("select: $select<br>\n");
	$res = mysql_query($select);
	print("<form action='loader.php'>\n");
	print("<select name='db_id'>\n");
	while($row = mysql_fetch_object($res))
	{
		print("<option value='$row->ss_id'>$row->name</option>\n");
	}
	print("</select>\n");
	print("<input name='search_terms'><br>\n");
	print("<input type='submit' value='Search'>\n");

}








function template($db_id,$search_terms,$sr)
{
	$time_start = microtime(true);
	$vsr = $sr->vendor_results[0];
	

	for($i=1;$i<20;$i++)
	{
		$title = "";
		$authors = "";
		$abstract = "";
		$source = "";
		$issn = "";
		$volume = "";
		$issue = "";
		$pages = "";
		$start_page = "";
		$date = "";
		$links = array();
		$doc_id = "";
		$full_text_available = "";
		$normalized_author = "";
		$normalized_date = "";

		$vsr->results[] = new vendor_search_result("",$title,$authors,$abstract,$source,$issn,$volume,$issue,$pages,$start_page,$date,$links,$doc_id,$full_text_available,$normalized_author,$normalized_date,$html_full_text,$rank);
	}

	$vsr->vendor_id = $db_id;
	$vsr->vendor_name = "Academic Search Premier";
	$vsr->total_results = 0;
	$time_end = microtime(true);
	$vsr->load_time = round(($time_end - $time_start)*1000);
	$sr->vendor_results[] = $vsr;
	return $sr;
}

?>