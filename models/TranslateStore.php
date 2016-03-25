<?php

namespace app\models;

/*
 * Helps store the translations in a flat file, database, or api
 */

/**
 * Description of TranslateStore
 *
 * @author adityamittal
 */
class TranslateStore {

  /**
   * Store translations by app
   */
  public function storeTranslationsByApp($appId, $translations){
    $filename = "Translations_$appId";
    $this->storeTranslationsInFlatFile($filename, $translations);
  }
  
  /**
   * Store translations by app
   */
  public function getTranslationsByApp($appId){
    $filename = "Translations_$appId";
    $this->getTranslationsFromFlatFile($filename);
  }
  
  /**
   * Store json encoded translations in flat file
   * @param type $file
   * @param type $translations
   */
  public function storeTranslationsInFlatFile($filename, $translations){
    file_put_contents($filename, json_encode($translations));
  }
  
  /**
   * Get the translations from the flat file
   */
  public function getTranslationsFromFlatFile($filename){
    return json_decode(file_get_contents($filename),true);
  }
  
  
}
