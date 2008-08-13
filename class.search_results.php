<?php

class search_results
{
	var $search_terms;
	var $vendor_dbs;
	var $vendor_results;
	
	function search_results($search_terms,$vendor_dbs)
	{
		$this->search_terms = $search_terms;
		$this->vendor_dbs = $vendor_dbs;
	}
	
	function save_result($parent_id)
	{
		//$fields = array('search_terms','vendor_dbs');
		//$values = array($this->search_terms,$this->vendor_dbs);
		//for($i=0;$i<count($values);$i++)
		//	$values[$i] = str_replace("'","\'",$values[$i]);
		//if(noDuplicate('search_cache',$fields,$values))
		//	$parent_id = insert('search_cache',$fields,$values);
		//else
		//{
		//	$select_search = "select * from search_cache where search_terms like '$this->search_terms'";
		//	$res_search = mysql_query($select_search);
		//	$cached_search_obj = mysql_fetch_object($res_search);
		//	$parent_id = $cached_search_obj->id;
		//}
		
		$this->parent_id = $parent_id;
		
		foreach($this->vendor_results as $vendor_result)
		{
			$fields = array('parent_id','vendor_id','total_results','load_time');
			$values = array($parent_id,$vendor_result->vendor_id,$vendor_result->total_results,$vendor_result->load_time);
			//for($i=0;$i<count($values);$i++)
			//		$values[$i] = str_replace("'","\'",$values[$i]);
			$select_dup = "select * from search_cache_vendors where parent_id like '$parent_id' and vendor_id like '$vendor_result->vendor_id'";
			$res_dup = mysql_query($select_dup);
			if(mysql_num_rows($res_dup)==0)
			{
				$search_id = insert('search_cache_vendors',$fields,$values);
				
				$results_collected = count($vendor_result->results);
				//print("collected: $results_collected<br>\n");
				if(count($vendor_result->results)>0)
				{
					foreach($vendor_result->results as $vsr)
					{
						$resolver_url = "";
						$url = "";
						foreach($vsr->links as $link)
						{
							//print("link type: [$link->type]<br>\n");
							if(!strcmp($link->type,'link resolver'))
								$resolver_url = $link->url;
							else
								$url = $link->url;
						}
						//$resolver_url = "test";
						$fields = array('search_id','title','authors','abstract','source','issn','volume','issue','pages','start_page','date','url','resolver_url','doc_id','full_text_available','normalized_author','normalized_date','html_full_text','rank');
						$values = array($search_id,$vsr->title,$vsr->authors,$vsr->abstract,$vsr->source,$vsr->issn,$vsr->volume,$vsr->issue,$vsr->pages,$vsr->start_page,$vsr->date,$url,$resolver_url,$vsr->doc_id,$vsr->full_text_available,$vsr->normalized_author,$vsr->normalized_date,$vsr->html_full_text,$vsr->rank);
						
						for($i=0;$i<count($values);$i++)
							$values[$i] = str_replace("'","\'",$values[$i]);
						insert('search_cache_results',$fields,$values);
					}
				}
				else
				{
					
				}
			}
			else
			{
				$dup = mysql_fetch_object($res_dup);
				$search_id = $dup->id;
			}
		}
	}
}

class vendor_result
{
	var $vendor_id;
	var $vendor_name;
	var $total_results;
	var $load_time;
	var $results;
	
	function add_vendor_results($results=array())
	{
		$this->results[] = new vendor_search_result();
	}
	

}

class vendor_search_result
{
	var $id;
	var $title;
	var $authors;
	var $abstract;
	var $source;
	var $issn;
	var $volume;
	var $issue;
	var $pages;
	var $start_page;
	var $date;
	var $doc_id;
	var $full_text_available;
	var $normalized_author;
	var $normalized_date;
	
	function vendor_search_result($id="",$title="",$authors=array(),$abstract="",$source="",$issn="",$volume="",$issue="",$pages="",$start_page="",$date="",$links=array(),$doc_id="",$full_text_available="",$normalized_author="",$normalized_date="",$html_full_text="",$rank="")
	{
		$this->id = $id;
		$this->title = $title;
		$this->authors = $authors;
		$this->abstract = $abstract;
		$this->source = $source;
		$this->issn = $issn;
		$this->volume = $volume;
		$this->issue = $issue;
		$this->pages = $pages;
		$this->start_page = $start_page;
		$this->date = $date;
		$this->links = $links;
		$this->doc_id = $doc_id;
		$this->full_text_available = $full_text_available;
		$this->normalized_author = $normalized_author;
		$this->normalized_date = $normalized_date;
		$this->html_full_text = $html_full_text;
		$this->rank = $rank;
	}
}


class author
{
	var $author;
}

class link
{
	var $url;
	var $type;
}

?>
