<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class EditXML {

	function add_element ($xml,$root,$name) {
	    $this->new_line ($xml,$root);
	    $base = $xml->createElement($name);
	    $symbol = $root->appendChild($base);
	    return $symbol;
	}
	function add_tx_element ($xml,$root,$name,$tx) {
	    $this->new_line ($xml,$root);
	    $base = $xml->createElement($name,$tx);
	    $symbol = $root->appendChild($base);
	    $this->new_line ($xml,$symbol);
	    return $symbol;
	}
	function add_cdata_element ($xml,$root,$name,$tx) {
	    $this->new_line ($xml,$root);
	    $base = $xml->createElement($name);
	    $base->appendChild($xml->createCDATASection( "\n".$tx."\n" ));
	    $symbol = $root->appendChild($base);
	    return $symbol;
	}
	function add_comment ($xml,$root,$comment) {
	        $this->new_line ($xml,$root);
		$CommentNode = $xml->createComment($comment);
  		$symbol = $root->appendChild( $CommentNode );
  		$this->new_line ($xml,$symbol);
  		return $symbol;
	}
	function new_line ($xml,$root) {
		$new_line=$xml->createTextNode("\n");
		$symbol = $root->appendChild($new_line);
		return $symbol;
	}
        function add_symbol_attribute($symbol) {
            	$symbol->setAttribute("IsArchivable", "no");
    		$symbol->setAttribute("IsPlaceable", "yes");
    		$symbol->setAttribute("MainGUID", $this->GUID);
    		$symbol->setAttribute("MigrationValue", "Normal");
		$symbol->setAttribute("Owner", "1196638531");
		$symbol->setAttribute("Signature", "1196644685"); 
		$symbol->setAttribute("Version", $this->version); 
        }
	function add_element_attribute($symbol,$attributes) {
		foreach ($attributes as $key=>$value) {
			$symbol->setAttribute($value["name"], $value["value"]);
			}
	
	}


}