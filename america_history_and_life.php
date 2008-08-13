<?php

function search_america_history_and_life($db_id,$search_terms,$sr)
{
	global $ebsco_username, $ebsco_password;
	
	$starting_record = 1;
	$num_records = 20;
	
	$time_start = microtime(true);
	$vsr = $sr->vendor_results[0];
	
	$auth = array();
	$auth['user'] = $ebsco_username;
	$auth['password'] = $ebsco_password;
	$host = "epserver.epnet.com/ahl";

	$conn = yaz_connect($host,$auth);
	yaz_syntax($conn, "usmarc");
	yaz_range($conn, $starting_record, $num_records);
	//print("query: @attr 1=4 " . urlencode($search_terms)."<br>\n");
	yaz_search($conn, "rpn", "@attr 1=4 " . urlencode($search_terms));
	yaz_wait();
	
	$error = yaz_error($conn);

	if (!empty($error))
	{
		// there was an error
		//print_r($error);
	}

	$total_results = yaz_hits($conn);
	
	for ($p = 1; $p <= $num_records; $p++)
	{
		$rec = yaz_record($conn, $p, "string");
		
		$authors = "";
		$html_full_text = "";
		$full_text_available = "no";
		$rank = $p;
		$links = array();
		
		//print("<pre>\n");
		//print_r($rec);
		//print("</pre>\n");

		
		if (empty($rec)) continue;
		
		$lines = explode("\n",$rec);
		foreach($lines as $line)
		{
			$marc_tag = substr($line,0,3);
			
			switch ($marc_tag)
			{
				case '001':
					$id = trim(substr($line,3));
					break;
				case '856':
					if(!strcmp($line,"856    \$i TEXT*"))
					{
						// html full text is present and can be collected from the marc 900 tags
						$full_text_available = "yes";
					}
					else if(!strcmp(substr($line,0,13),"856    \$a PDF"))
					{
						//print("<b>this record may contain a pdf link to the resource</b><br>\n");
						$pdf = substr($line,27);
						$l = new link();
						$l->url = $pdf;
						$l->type = "direct";
						$links[] = $l;
						$full_text_available = "yes";
					}
					break;
				case '245':
					$parts_245 = explode("\$",$line);
					foreach($parts_245 as $part_245)
					{
						$char_245 = substr($part_245,0,1);
						switch($char_245)
						{
							case 'a':
								$title = substr($part_245,2);
								break;
						}						
					}
					//print("TITLE: [$title]<br>\n");
					break;
				case '520':
					$parts_520 = explode("\$",$line);
					foreach($parts_520 as $part_520)
					{
						$char_520 = substr($part_520,0,1);
						switch($char_520)
						{
							case 'a':
							$abstract = substr($part_520,2);
							break;
						}
					}
					//print("abstract: [$abstract]<br>\n");
					break;
				case '773':
					$parts_773 = explode("\$",$line);
					foreach($parts_773 as $part_773)
					{
						$char_773 = substr($part_773,0,1);
						switch($char_773)
						{
							case 'g':
								$parts_773g = explode(",",substr($part_773,2));
								//print("<pre>\n");
								//print_r($parts_773g);
								//print("</pre>\n");
								$date_stamp = strtotime($parts_773g[0]);
								if($date_stamp === false)
									$date = $parts_773g[0];
								else
									$date = date('M Y',$date_stamp);
								
								$volume_issue = trim($parts_773g[1]);
								$issue_pos = strpos($volume_issue,"Issue");
								if($issue_pos === false)
									$volume = $volume_issue;
								else
								{
									$volume = substr($volume_issue,0,$issue_pos-1);
									$issue = substr($volume_issue,$issue_pos);
								}
								$pages = trim($parts_773g[2]);
								break;
							case 't':
								$source = substr($part_773,2);
								//print("source: $source<br>\n");
								break;
						}
					}
					//print("<pre>\n");
					//print_r($parts_773);
					//print("</pre>\n");
					break;
				case '100':
					$parts_100 = explode("\$",$line);
					foreach($parts_100 as $part_100)
					{
						$char_100 = substr($part_100,0,1);
						switch($char_100)
						{
							case 'a':
								$author = substr($part_100,2);
								if(strcmp($authors,''))
									$authors .= "; " . $author;
								else
									$authors = $author;
								break;
						}						
					}
					//print("authors: $authors<br>\n");
					break;
				case '900':
					$html_full_text .= str_replace("900    \$a ","",$line)."<br>\n";
					//print("full text: $html_full_text<br>\n");
					break;
			}
		}
		
		if(strcmp($html_full_text,'') && count($links)==0)
		{
			$l = new link();
			$l->url = "local";
			$l->type = "direct";
			$links[] = $l;
		}
		else if(count($links)==0)
		{
			$l = new link();
			$l->url = "http://search.ebscohost.com/login.aspx?direct=true&AuthType=ip,url,cookie,uid&an=$id&db=ahl&scope=site&site=ehost";
			$l->type = "direct";
			$links[] = $l;
		}
		
		$vsr->results[] = new vendor_search_result("",$title,$authors,$abstract,$source,$issn,$volume,$issue,$pages,$start_page,$date,$links,$doc_id,$full_text_available,$normalized_author,$normalized_date,$html_full_text,$rank);
	}
	$vsr->vendor_id = $db_id;
	$vsr->vendor_name = "Academic Search Premier";
	$vsr->total_results = $total_results;
	$time_end = microtime(true);
	$vsr->load_time = round(($time_end - $time_start)*1000);
	//$sr->vendor_results[0] = $vsr;
	return $sr;
}


?>