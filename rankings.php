<?php

require_once("includes/config.inc.php");
require_once("includes/connect.php");
require_once("http://www.lib.pdx.edu/web_templates/library_site_header.inc.php");
print("<div id='PSUContent'>\n");
print("<h1>Acaweb Vendor Load Time Rankings</h1>\n");

$select_dbs = "select * from vendor_dbs where active like '1'";
$res_dbs = mysql_query($select_dbs);
while($db_obj = mysql_fetch_object($res_dbs))
{
	$all_dbs[$db_obj->ss_id] = $db_obj;
}

$select_vendors = "SELECT vendor_id,max(load_time) as max,min(load_time) as min, avg(load_time) as ave FROM `search_cache_vendors` group by vendor_id order by ave";
$res_vendors = mysql_query($select_vendors);
print("<table border style='font-size:12px;'><tr><th></th><th>Vendor</th><th>Ave Load Time</th><th>Min Load Time</th><th>Max Load Time</th></tr>\n");
while($vendor = mysql_fetch_object($res_vendors))
{
	$count++;
	$vendor_name = $all_dbs[strtoupper($vendor->vendor_id)]->name;
	$custom_loader = $all_dbs[strtoupper($vendor->vendor_id)]->custom_loader;
	unset($all_dbs[$vendor->vendor_id]);
	$ave = number_format(($vendor->ave/1000),2) . " sec";
	$min = number_format(($vendor->min/1000),2) . " sec";
	$max = number_format(($vendor->max/1000),2) . " sec";
	print("<tr><td align=right>$count</td><td>");
	if(strcmp($custom_loader,''))
		print("<b>$vendor_name</b>");
	else
		print("$vendor_name");
	print("</td><td align=right>$ave</td><td align=right>$min</td><td align=right>$max</td></tr>\n");
}
print("</table>\n");

if(!strcmp($_GET['debug'],'1'))
{
	print("<pre>\n");
	print_r($all_dbs);
	print("</pre>\n");
}

print("</div>\n");
require_once("http://www.lib.pdx.edu/web_templates/library_site_footer.inc.php");

?> 