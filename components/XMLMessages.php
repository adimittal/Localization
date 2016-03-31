<?php

class XMLMessages {
  
  
  public function loadFile($folder, $file) {
    $xml = simplexml_load_file($folder . "/" . $file) or die("Error: Cannot create xml object");
    return $xml;
  }
  
  public function loadClientStrings() {
    
    
    
    
  }
  
  
  
}