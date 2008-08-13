<?php

require_once("includes/connect.php");
require_once("includes/mysql_functions.php");
require_once("class.xmlparser.php");
require_once("class.search_results.php");

require_once("http://www.lib.pdx.edu/web_templates/library_site_header.inc.php");
print("<div id='PSUContent'>\n");

$db_ids = $_GET['db_ids'];
$search_terms = $_GET['search_terms'];
$search_index = "anyField";

$select_dbs = "select * from vendor_dbs where active like '1'";
$res_dbs = mysql_query($select_dbs);
while($db_obj = mysql_fetch_object($res_dbs))
{	
	$all_dbs[$db_obj->ss_id] = $db_obj;
}

//print("<pre>\n");
//print_r($_GET);
//print("</pre>\n");

if(count($db_ids) > 0 && strcmp($search_terms,''))
{

// save the search result set
$fixed_search_terms = str_replace("'","\'",$search_terms);
$fields = array('search_terms','vendor_dbs');
$values = array($fixed_search_terms,implode($db_ids,","));

//if(noDuplicate('search_cache',$fields,$values))
//{
	$parent_id = insert('search_cache',$fields,$values);
//}
//else
//{
//	$select_search = "select * from search_cache where search_terms like '$fixed_search_terms'";
//	$res_search = mysql_query($select_search);
//	$cached_search_obj = mysql_fetch_object($res_search);
//	$parent_id = $cached_search_obj->id;
//}



?>

<div id='searchStatusWindow'>
<table width=600 style='border:1px solid #000000;'><tr><td bgcolor='#999999' style='font-size:22pt; font-weight:bold; color:#FFFFFF'>Academic Web Database</td><td bgcolor='#999999' style='font-size:22pt; font-weight:bold; color:#FFFFFF'>Results</td></tr>
<?php
	for($i=0;$i<count($db_ids);$i++)
	{
		print("<tr><td>".$all_dbs[$db_ids[$i]]->name."</td><td width='100'><img src='http://digital.lib.pdx.edu/flakus/acaweb/img/loading_orange.gif'></td></tr>\n");
	}
?>
</table>
</div>

<script src="js/ajax.js" type="text/javascript"></script>
<script>
var intervalID = setInterval("updateDisplay();", 1500);
var searchID = "<?php print($parent_id); ?>";

// setup ajax calls
var database_ids = new Array();
var database_names = new Array();
var results_arr = new Array();
var timeout = 10;
var process_time = 0;

function goToResults()
{
	document.location.href="results.php?search_id=<?php print($parent_id); ?>";
}

<?php
	for($i=1;$i<=count($db_ids);$i++)
	{
		$db_id = $db_ids[$i-1];
		$db_name = $all_dbs[$db_id]->name;
		print("database_ids.push(\"$db_id\");\n");
		print("database_names.push(\"$db_name\");\n");
		print("results_arr.push(\"\");\n");
		print("var http$i = createRequestObject();\n");
		print("http$i.open('get', 'loader.php?parent_id=$parent_id&db_id=".$db_ids[$i-1]."&search_terms=$search_terms');\n");
		print("http$i.onreadystatechange = collectResponse$i;\n");
		print("http$i.send(null);\n");
		print("function collectResponse$i() {\n");
		print("   if(http$i.readyState == 4){\n");
		print("      handleResponse(http$i.responseText);\n");
		print("   }\n");
		print("}\n");
	}
?>

</script>


<form>
<input type=button value='Go To Search Results Now' onClick='javascript:goToResults();' />
</form>



<?php

}
else
{

	$select = "select * from vendor_dbs where active like '1' order by name";
	//print("select: $select<br>\n");
	$res = mysql_query($select);
	print("<center>\n");
	print("<form action='index.php'>\n");
	print("<b>ACADEMIC WEB SEARCH: </b><input name='search_terms' value='$search_terms' size=30>\n");
	print("<input type='submit' value='Search'>\n");
	print("<table style='font-size:13px;' width=950><tr>\n");
	$db_count = mysql_num_rows($res);
	$num_cols = 2;
	$per_col = ceil($db_count/$num_cols);
	$in_col = 0;
	while($row = mysql_fetch_object($res))
	{
		$in_col++;
		if($in_col == 1)
			print("<td>");
			
		if(strcmp($row->custom_loader,''))
			print("&nbsp; <input type=checkbox name='db_ids[]' value='$row->ss_id'><b>$row->name</b><br>\n");
		else
			print("&nbsp; <input type=checkbox name='db_ids[]' value='$row->ss_id'>$row->name<br>\n");
		
		if($in_col == $per_col)
		{
			print("</td>");
			$in_col = 0;
		}
	}
	if($in_col > 0 && $in_col < $per_col)
		print("</td>");

	print("</tr></table></center><br>\n");


}
print("</div>\n");
require_once("http://www.lib.pdx.edu/web_templates/library_site_footer.inc.php");

?>
