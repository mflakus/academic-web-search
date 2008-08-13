<?php

	function noDuplicate($table,$fields,$values)
	{
		$select = "select * from $table where ";
		for($i=0;$i<count($fields);$i++)
		{
			if($i==0)
				$select .= "$fields[$i] like '$values[$i]'";
			else
				$select .= " AND $fields[$i] like '$values[$i]'";
		}
		//print("select: $select<br>\n");
		$res = mysql_query($select);
		$count = mysql_num_rows($res);
		//print("count: $count<br>\n");	
		if($count==0)
			return true;
		else
			return false;
	}


	function insert($table, $fields, $values)
	{
		$query = "INSERT INTO `$table` (";
		for($i=0; $i<count($fields); $i++)
		{
			$query .= "`" . $fields[$i] . "`";
			if($i+1 < count($fields))
				$query .= ",";
		}
		$query .= ") VALUES (";
		for($j=0; $j<count($values); $j++)
		{
			$query .= "'" . $values[$j] . "'";
			if($j+1 < count($values))
				$query .= ",";
		}
		$query .= ")";
		//print("insert query: $query<br>\n");
		$res = mysql_query($query);
		if(!$res)
		{
			print("There was an error inserting this record into the database.<br>\n");
			print("SQL Query: $query<br>\n");
		}
		else
		{
			$history_record = "insert into `project_history` (`table`,`operation`,`query`,`changed_by`) VALUES ('$table','insert','".str_replace("'","\'",$query)."','".$_SERVER['PHP_AUTH_USER']."')";
			mysql_query($history_record);
		}

		return mysql_insert_id();
	}

//	function delete($table, $where)
//	{
//		global $username;
//		$query = "DELETE FROM `$table` WHERE $where";
//
//		print("delete query: $query<br>\n");
//	}

	function update($table, $field, $value, $where,$ids=array())
	{
		global $username;
		// TODO: check if most recent update is the same as the current update (reload bug)
		
		if(strcmp($where,''))
		{
			$query = "UPDATE `$table` SET `$field` = '$value' $where";
			//print("query: $query<br>\n");
			$res = mysql_query($query);
			if(!$res)
			{
				print("There was an error updating this record.<br>\n");
				print("SQL Query: $query<br>\n");
				return 0;
			}
			else
			{
				$project_id = $ids['project_id'];
				$task_id = $ids['task_id'];
				$note_id = $ids['note_id'];
				$document_id = $ids['document_id'];
				$participant_id = $ids['participant_id'];
				$history_record = "insert into `project_history` (`table`,`operation`,`query`,`project_id`,`task_id`,`note_id`,`document_id`,`participant_id`,`changed_by`) VALUES ('$table','update','".str_replace("'","\'",$query)."','$project_id','$task_id','$note_id','$document_id','$participant_id','".$_SERVER['PHP_AUTH_USER']."')";
				//print("history_record: $history_record<br>\n");
				mysql_query($history_record);
				return 1;
			}
		}
		return 0;
	}

	function select($table, $fields, $where, $order, $limit)
	{
		$query = "SELECT ";
		for($i=0; $i<count($fields); $i++)
		{
			$query .= "`" . $fields[$i] . "`";
			if($i+1 < count($fields))
				$query .= ",";
		}
		$query .= " FROM `$table` $where $order $limit";
		print("select: $query<br>\n");
	}

	//$val[] = 'mike';
	//$val[]='flakus';
	//$name[]='first_name';
	//$name[]='last_name';
	//insert('clients', $name, $val);
	//update('clients','last_name','flakusenski',"WHERE last_name like 'flakus'");
	//select('clients',$name,"WHERE `last_name` like 'flakus'",'','');
?>
