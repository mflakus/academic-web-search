<?php

require_once("includes/config.inc.php");
require_once("includes/connect.php");
require_once("http://www.lib.pdx.edu/web_templates/library_site_header.inc.php");
print("<div id='PSUContent'>\n");

$id = $_GET['id'];
$select = "select * from search_cache_results where id like '$id'";
$res = mysql_query($select);
$article = mysql_fetch_object($res);
print("<h1>$article->title</h1>\n");
print("$article->html_full_text<br>\n");

print("</div>\n");
require_once("http://www.lib.pdx.edu/web_templates/library_site_footer.inc.php");

?>