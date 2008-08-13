<?php

function search_google_scholar($db_id,$search_terms,$sr)
{
	$time_start = microtime(true);
	$vsr = $sr->vendor_results[0];
	$num_of_results = 20;
	$service_url = "http://scholar.google.com/scholar?hl=en&lr=&q=".urlencode($search_terms)."&num=$num_of_results";
	$contents = file_get_contents($service_url);
	$result_count_pos = strpos($contents,"<font size=-1 color=>Results");
	$result_count_line = strip_tags(substr($contents,$result_count_pos,300),'<b>');
	$result_strings = explode("<p class=g>",substr($contents,$result_count_pos));
	for($i=1;$i<count($result_strings);$i++)
	{
		$citation_only = 0;
		$title = "";
		$url = "";
		$resolver_url = "";
		$links = array();
		$rank = $i;
		
		$preg = "/a[\s]+[^>]*?href[\s]?=[\s\"\']+(.*?)[\"\']+.*?>"."([^<]+|.*?)?<\/a>/";
		preg_match_all(trim($preg),$result_strings[$i], $out, PREG_PATTERN_ORDER);
		$keys = $out[1];
		$values = $out[2];		

		for($j=0;$j<count($keys);$j++)
		{
			$link = $keys[$j];
			$text = $values[$j];
			//print("text: $text ------- link: $link<br>\n");
			if(!strcmp($title,''))
			{
				$title = $text;
				if(!strcmp(substr($title,0,9),"Cited by "))
				{
					$citation_only = 1;
					break;
				}
				$l = new link();
				$l->url = $link;
				$l->type = "direct";
				$links[] = $l;
			}
			else if(strpos($link,'http://wq5rp2ll8a.scholar.serialssolutions.com') !== false)
			{
				$resolver_url = $link;
				$l = new link();
				$l->url = $link;
				$l->type = "link resolver";
				$links[] = $l;
			}
		}

		if($citation_only == 1)
			continue;
			
		$authors_pos_start = strpos($result_strings[$i],"<span class=\"a\">");
		$rest = substr($result_strings[$i],$authors_pos_start);
		$authors_pos_end = strpos($rest,"</span>");
		$authors_line = substr($rest,0,$authors_pos_end);
		$authors_line_parts = explode("-",$authors_line);
		$authors = strip_tags($authors_line_parts[0]);
		$source = strip_tags($authors_line_parts[2]);
		$date_start_pos = strpos($authors_line_parts[1],",");
		if($date_start_pos !== false) $date = trim(substr($authors_line_parts[1],$date_start_pos+1));
		$abstract_block = substr($rest,$authors_pos_end+11);
		$abstract_end_pos = strpos($abstract_block,"<br><a class=fl");
		$abstract = strip_tags(substr($abstract_block,0,$abstract_end_pos));

		$vsr->results[] = new vendor_search_result("",$title,$authors,$abstract,$source,$issn,$volume,$issue,$pages,$start_page,$date,$links,$doc_id,$full_text_available,$normalized_author,$normalized_date,$html_full_text,$rank);
	}

	preg_match_all("|<[^>]+>(.*)</[^>]+>|U",$result_count_line,$out, PREG_SET_ORDER);
	$total_results = str_replace(",","",$out[2][1]);
	//print("[$contents]<br>\n");

	$vsr->vendor_id = $db_id;
	$vsr->vendor_name = "Google Scholar";
	$vsr->total_results = $total_results;
	$time_end = microtime(true);
	$vsr->load_time = round(($time_end - $time_start)*1000);
	//$sr->vendor_results[] = $vsr;
	return $sr;
}

?>
