<?php

class XMLParser
{
   var $parser;
   var $filePath;
   var $document;
   var $currTag;
   var $tagStack;
   
   function XMLParser($path)
   {
       $this->parser = xml_parser_create();
	   $this->filePath = $path;
	   $this->document = array();
	   $this->currTag =& $this->document;
	   $this->tagStack = array();
   }
   
   function parse()
   {
       xml_set_object($this->parser, $this);
       xml_set_character_data_handler($this->parser, 'dataHandler');
       xml_set_element_handler($this->parser, 'startHandler', 'endHandler');
       
	   if(!($fp = fopen($this->filePath, "r")))
       {
           die("Cannot open XML data file: $this->filePath");
           return false;
       }
   
       while($data = fread($fp, 4096))
       {
           if(!xml_parse($this->parser, $data, feof($fp)))
           {
               die(sprintf("XML error: %s at line %d",
                           xml_error_string(xml_get_error_code($this->parser)),
                           xml_get_current_line_number($this->parser)));
           }
       }
   
       fclose($fp);
   xml_parser_free($this->parser);
   
       return true;
   }
   
   function startHandler($parser, $name, $attribs)
   {
       if(!isset($this->currTag[$name]))
           $this->currTag[$name] = array();
       
       $newTag = array();
       if(!empty($attribs))
           $newTag['attr'] = $attribs;
       array_push($this->currTag[$name], $newTag);
       
       $t =& $this->currTag[$name];
       $this->currTag =& $t[count($t)-1];
       array_push($this->tagStack, $name);
   }
   
   function dataHandler($parser, $data)
   {
       $data = trim($data);
       
       if(!empty($data))
       {
           if(isset($this->currTag['data']))
               $this->currTag['data'] .= $data;
           else
               $this->currTag['data'] = $data;
       }
   }
   
   function endHandler($parser, $name)
   {
       $this->currTag =& $this->document;
       array_pop($this->tagStack);
       
       for($i = 0; $i < count($this->tagStack); $i++)
       {
           $t =& $this->currTag[$this->tagStack[$i]];
           $this->currTag =& $t[count($t)-1];
       }
   }
}

?>