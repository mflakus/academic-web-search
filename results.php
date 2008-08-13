<style>
a {color:blue;}
</style>

<?php

require_once("includes/connect.php");
require_once("http://www.lib.pdx.edu/web_templates/library_site_header.inc.php");

$search_id = $_GET['search_id'];

$select_dbs = "select * from vendor_dbs where active like '1'";
$res_dbs = mysql_query($select_dbs);
while($db_obj = mysql_fetch_object($res_dbs))
{
	$all_dbs[$db_obj->ss_id] = $db_obj;
}


if(strcmp($search_id,''))
{

	$select = "select * from search_cache where id like '$search_id'";
	$res = mysql_query($select);
	$result_set = mysql_fetch_object($res);
	$search_terms = $result_set->search_terms;
	$search_vendor_dbs = $result_set->vendor_dbs;
	
	$select_vendors = "select * from search_cache_vendors where parent_id like '$result_set->id'";
	$res_vendors = mysql_query($select_vendors);
	$all_vendors = array();
	while($vendor = mysql_fetch_object($res_vendors))
	{
		$select_results = "select * from search_cache_results where search_id like '$vendor->id'";
		$res_results = mysql_query($select_results);
		$vendor_name = $all_dbs[$vendor->vendor_id];
		$total_results += $vendor->total_results;
		$all_search_vendors[] = $vendor->vendor_id;
		while($result = mysql_fetch_object($res_results))
		{
			$result->vendor_name = $vendor_name->name;
			$all_results[] = $result;
		}
	}
	$result_count = count($all_results);
	
	print("<div id='PSUContent'>\n");
	print("<form action='index.php'>\n");
	foreach(explode(",",$search_vendor_dbs) as $vendor)
		print("<input type='hidden' name='db_ids[]' value='$vendor'>\n");
	print("<table width=878><tr valign=center><td>\n");
	print("ACADEMIC WEB SEARCH: <input name='search_terms' size='30' value=\"$search_terms\"> <input type=submit value='Search'>\n");
	print("</td><td>\n");
	print("<a href='index.php?search_terms=$search_terms'>Select different databases</a><br>\n");
	print("<a href='index.php'>Start Over</a><br>\n");
	print("</td></tr></table>\n");
	print("</form>\n");
	
	if($result_count > 0)
	{
		print("<table bgcolor='FFFFCC' width='100%'><tr><td><b>SEARCH RESULTS</b></td><td align=right>Results <b>1-$result_count</b> of about <b>$total_results</b> for <b>$search_terms</b></td></tr></table><br>\n");
	
		foreach($all_results as $result)
		{
			print("<table width=800><tr><td>");
			if(strcmp($result->url,''))
			{
				if(!strcmp($result->url,'local'))
					print("<a href='view_full_text.php?id=$result->id'><b>$result->title</b></a>");
				else
					print("<a href='$result->url'><b>$result->title</b></a>");
			}
			else
				print("<b>$result->title</b>");
			print("</td>");
			if(strcmp($result->resolver_url,'')) print("<td align=right><a href='$result->resolver_url'><img src='img/resolver.gif' border=0 /></a></td>\n");
			print("</tr></table>\n");
			if(strcmp($result->abstract,''))
				print("$result->abstract<br>\n");
			print("<font color='green'><b>\n");
			if(strcmp($result->authors,'')) print("$result->authors, ");
			if(strcmp($result->source,'')) print("$result->source, ");
			if(strcmp($vendor_name->name,'')) print("$result->vendor_name ");
			if(strcmp($result->date,'')) print("- $result->date");
			print("</b></font><br>\n");
			print("<br>\n");	

		}
	}
	else
	{
		print("<br><h2>Unfortunately no search results were found for your search terms.</h2><br>\n");
	}
	print("</div>\n");
}

require_once("http://www.lib.pdx.edu/web_templates/library_site_footer.inc.php");

?> 
