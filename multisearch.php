
n search_multisearch($db_id,$search_terms,$sr)
{
	$time_start = microtime(true);
	$search_index = "anyField";
	$search_terms = $search_index . "+any+\"" . urlencode($search_terms) . "\"";
	$service_url = "http://wq5rp2ll8a.cs.xml.serialssolutions.com/sru?version=1.1&x-cs-databases=$db_id&operation=searchRetrieve&query=$search_terms";
	$vsr = $sr->vendor_results[0];
	//print("service url: $service_url<br>\n");
	
	$results = new XMLParser($service_url);
	$results->parse();

	if(!isset($results->document["SRW:SEARCHRETRIEVERESPONSE"][0]["SRW:RECORDS"][0]["SRW:RECORD"][0]["SRW:RECORDDATA"][0]["CS:SEARCHPROFILE"][0]["CS:SEARCHPROFILE"]))
	{
		print("<pre>\n");
		print_r($results);
		print("</pre>\n");
	}
	else
	{
		foreach($results->document["SRW:SEARCHRETRIEVERESPONSE"][0]["SRW:RECORDS"][0]["SRW:RECORD"][0]["SRW:RECORDDATA"][0]["CS:SEARCHPROFILE"][0]["CS:SEARCHPROFILE"] as $database)
		{
			$db_name = $database["CS:SEARCHPROFILE"][0]["attr"]["NAME"];
			$total_results = $database["CS:SEARCHPROFILE"][0]["CS:CITATIONCOUNT"][1]["data"];
			//print("$db_name ($total_results)<br>\n");
		}
	}

	$downloaded_records = count($results->document["SRW:SEARCHRETRIEVERESPONSE"][0]["SRW:RECORDS"][0]["SRW:RECORD"]);
	//print("Number of Records downloaded so far: $downloaded_records<br>\n");

	for($i=1;$i<$downloaded_records;$i++)
	{
		$record = $results->document["SRW:SEARCHRETRIEVERESPONSE"][0]["SRW:RECORDS"][0]["SRW:RECORD"][$i]["SRW:RECORDDATA"][0]["CS:CITATION"][0];
		$id = $record["DC:IDENTIFIER"][0]["data"];
		$rank = $i;
		$title = $record["DC:TITLE"][0]["data"];
		$authors = $record["DC:CREATOR"][0]["data"];
		$authors_arr = array();
		for($j=0;$j<count($record["DC:CREATOR"]);$j++)
		{
			$author = $record["DC:CREATOR"][$j]["data"];
			$authors_arr[$author] = $author;
		}
		$authors = implode($authors_arr,"; ");
		if(strlen($authors)>255) $authors = substr($authors,0,250) . "...";
		$source = $record["DC:SOURCE"][0]["data"];
		$issn = $record["CS:ISSN"][0]["data"];
		$volume = $record["CS:VOLUME"][0]["data"];
		$issue = $record["CS:ISSUE"][0]["data"];
		$pages = $record["CS:PAGES"][0]["data"];
		$start_page = $record["CS:SPAGE"][0]["data"];
		$date = $record["DCTERMS:ISSUED"][0]["data"];
		//$type = $record["DC:TYPE"][0]["data"];
		$abstract = $record["DCTERMS:ABSTRACT"][0]["data"];
		$links = array();
		if(isset($record["CS:URL"]))
		{
			foreach($record["CS:URL"] as $l)
			{
				$link = new link();
				$link->url = $l["data"];
				$link->type = $l["attr"]["TYPE"];
				$links[] = $link;
			}
		}
		//$provider_id = $record["CS:PROVIDERID"][0]["data"];
		//$provider_name = $record["CS:PROVIDERNAME"][0]["data"];
		//$database_id = $record["CS:DATABASEID"][0]["data"];
		//$database_name = $record["CS:DATABASENAME"][0]["data"];
		$doc_id = $record["CS:DOCID"][0]["data"];
		$full_text_available = $record["CS:FULLTEXTAVAILABLE"][0]["data"];
		$normalized_data = $record["CS:NORMALIZEDDATA"][0];
		$normalized_author = $normalized_data["DC:CREATOR"][0]["data"];
		$normalized_date = $normalized_data["DCTERMS:ISSUED"][0]["data"];;
		
		$vsr->results[] = new vendor_search_result($id,$title,$authors,$abstract,$source,$issn,$volume,$issue,$pages,$start_page,$date,$links,$doc_id,$full_text_available,$normalized_author,$normalized_date,$html_full_text,$rank);
		//if(strcmp(trim($vsr->total_results),''))
		//	$vsr->total_results = 0;
	}
	
	$vsr->vendor_id = $db_id;
	$vsr->vendor_name = $db_name;
	$vsr->total_results = $total_results;
	$time_end = microtime(true);
	$vsr->load_time = round(($time_end - $time_start)*1000);
	
	//$sr->vendor_results[] = $vsr;
	return $sr;
}


?>
