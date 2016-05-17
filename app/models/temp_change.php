<?php

require_once ("base.php");
require_once ("app/models/xml.php");
require_once ("app/models/xml-edit.php");

class ChangeXML extends Base {
    function __construct() {
        
      $dir = $_SERVER["DOCUMENT_ROOT"]."/XML creater/public/XML";
      $files = scandir($dir,1);
      
      $data["title"]="Edit XML"; 
      if (isset($_GET["file"])) {
      	$_SESSION["file_id"] = $_GET["file"];
      }
      //var_dump($_SESSION);
     
      if (!empty($_SESSION["file_id"])) {
        $data["file_id"] = $_SESSION["file_id"];
      	$data["file_name"] = $files[$_SESSION["file_id"]];
      	$xml = new DomDocument("1.0","UTF-8");
   	$xml->load($dir."/".$data["file_name"]);
   	$xml->preserveWhiteSpace = false;
    	$xml->formatOutput = true;
   	$Master_script = $xml->getElementsByTagName("Script_1D")->item(0);
	$data["master_script"] =$Master_script->textContent;
	$Script_3D = $xml->getElementsByTagName("Script_3D")->item(0);
	$data["script_3d"] =$Script_3D->textContent;
	$parameter = $xml->getElementsByTagName("Parameters")->item(0);
	$val = "";
	
	foreach ($parameter->childNodes as $childNode) { 
		if ($childNode->localName!="") {
			$parameter_variable = $childNode->getAttribute("Name");
			$parameter_name = $childNode->getElementsByTagName("Description")->item(0)->textContent;
			$parameter_value = $childNode->getElementsByTagName("Value")->item(0)->textContent;
			if ($childNode->getElementsByTagName("ParFlg_Child")->item(0)) {
				$flag = "cu flag";
			} else {
				$flag = "fara flag";
			}
			$val.=str_replace("\n","",$childNode->localName." - ".$parameter_variable." - ".$parameter_name." - ".$parameter_value." - ".$flag);
			$val.="\n";
		}
	}
	
	$data["parameters"] =$val;
	$rows = explode("\n", $val);
	
      } 
      
      if (!empty($_FILES)) {
        $target_dir = $dir."/";
      	$file_name=basename($_FILES["fileToUpload"]["name"]);
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_dir.$_FILES["fileToUpload"]["name"])) {
    		  echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
    		  header("Location: index.php?page=change_xml");
    	} else {
    		   echo "Sorry, there was an error uploading your file.";
    	}
      }
      
      if (isset($_GET["save"])) {
      	//$this->change_cdata_value($xml,$Master_script,$_POST["master_script"]);

      	$nodesToDelete = array(); 
	foreach($parameter->childNodes as $child) 
	{ 
	  $nodesToDelete[] = $child; 
	} 
	foreach($nodesToDelete as $node) 
	{ 
	  $node->parentNode->removeChild($node); 
	} 
      	$newText = $xml->createCDATASection($_POST["master_script"]);
      	$Master_script->replaceChild($newText,$Master_script->childNodes->item(0));
      	$newText = $xml->createCDATASection($_POST["3d_script"]);
      	$Script_3D->replaceChild($newText,$Script_3D->childNodes->item(0));
      	
      	$this->convert_par_xml($xml,$parameter,$_POST["parameters"]);
      	$xml->save($dir."/".$data["file_name"]);
      }  else {         
      include "app/views/upload_xml_view.php";
      echo $this->list_files($files);       
      $this->render ("app/views/head_view.php",$data);
      include "app/views/menu_view.php";
      echo "script este".$xml_read->Symbol[0]->Script_3d;
      $this->render ("app/views/xml_edit_view.php",$data);
      include "app/views/footer_view.php";
      }

    }
    
    function list_files ($files) {
    
    	$view = "<table class='file_list'>";

	foreach ($files as $key=>$values) {
	    	if ($values!=".."&&$values!=".") {
	    		$view .="<tr><td>$values</td><td><button data-file_id='$key'>EDIT</button></td></tr>";
	    		
	    	}
	}

    	$view .= "</table>";
    	return $view;
    }
    function convert_par_xml ($xml,$parameter,$val) {
    	$file = new EditXML ();
        $rows = explode("\n", $val);
	echo "<pre>";
	var_dump($rows);
	echo "</pre>";
	
	foreach ($rows as $key=>$value) {
		$new_key = $this->rstrstr($value," - ");
		$keys = explode (" - ",$value);
		var_dump($keys);
		$element = $keys[0];		
		$element_attr[0]["name"] = "Name";
		$element_attr[0]["value"] = $keys[1];	
		if ($element!="") {
			echo "Element este".$element;
			$variable = $file ->add_element ($xml,$parameter,$element);
			$file ->add_element_attribute($variable,$element_attr);
			$description = $file ->add_cdata_element ($xml,$variable,"Description",$keys[2]);
			$file ->new_line ($xml,$description);
			if ($element!="Title") {
				if ($keys[4]=="cu flag") {
					$flags = $file ->add_element ($xml,$variable,"Flags");
					$parflags = $file ->add_element ($xml,$flags,"ParFlg_Child");
					$file ->new_line ($xml,$flags);
				}
				$value = $file ->add_cdata_element ($xml,$variable,"Value",$keys[3]);
				$file ->new_line ($xml,$value);
			}
			$file ->new_line ($xml,$variable);
		}
		
	}
	
	
    }
	function rstrstr($text,$needle)
	    {
	        return substr($text, 0,strpos($text, $needle));
	    }
    
}

?>