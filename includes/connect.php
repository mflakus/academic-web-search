<?php

	// connect file for SQL Server --> PHP


function connect($db_host,$db_user,$db_pass,$db_name)
{
	if(!$link = mysql_connect($db_host, $db_user, $db_pass))
	{
		$result = 0;
		print("Error connecting to MySQL Server [$db_host] with user account [$db_user]!<br>\n");
	}
	else
	{
		if(!$conn = mysql_select_db($db_name,$link))
		{
			print("error selecting database<br>\n");
		}
	}
}

connect($mysql_host,$mysql_username,$mysql_password,$mysql_db);

?>

