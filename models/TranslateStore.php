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

  private $db;

  public function __construct() {
    $this->db = new \app\components\DB;
  }

  /**
   * update the messages in database
   */
  public function dbupdate() {
    $configmanager = new \app\components\ConfigManager();
    $projects = $configmanager->getProjectNames();

    foreach ($projects as $p) {
      $slug = $configmanager->getSlug($p);
      $dir = $configmanager->getMessageDataPath($slug);
      $subdirs = $this->getSubdirectories($dir);
      foreach ($subdirs as $sub) {
        foreach (glob("$sub/*") as $filepath) {
          $language = substr($sub, strrpos($sub, '/') + 1);
          $this->insertFilePathIntoMessages($filepath, $language, $slug);
        }
      }
    }
  }

  private function getFileExt($filename) {
    $path_parts = pathinfo($filename);
    return $path_parts['extension'];
  }

  /**
   * 
   * @param type $filepath - ex: .../adf/adfaf.php
   * @param type $language - the language of the translation string
   * @param type $platform_key - slug of the project
   */
  private function insertFilePathIntoMessages($filepath, $language, $platform_key) {
    $ext = $this->getFileExt($filepath);

    switch ($ext) {
      case 'php' :
        $contents = include $filepath;
        break;
      case 'strings' :
        preg_match_all('/"(.*)".*=.*"(.*)"/i', file_get_contents($filepath), $matches);
        $keys = $matches[1];
        $values = $matches[2];
        $contents = array_combine($keys, $values);
        break;
      case 'xml' :
        preg_match_all('/name="(.*)">(.*)<\/string>/i', file_get_contents($filepath), $matches);
        $keys = $matches[1];
        $values = $matches[2];
        $contents = array_combine($keys, $values);
        break;
      default:
        $contents = []; //we ignore html files and files without extensions
    }

    $filename = substr($filepath, strrpos($filepath, '/') + 1);

    foreach ($contents as $key => $string) {

      $createdtime = date('Y-m-d h:i:s', time());
      $updatedtime = date('Y-m-d h:i:s', time());
      $bindparams = [
        ':platform_key' => "$platform_key",
        ':key' => "$key",
        ':string' => "$string",
        ':filepath' => "$filepath",
        ':filename' => "$filename",
        ':language' => "$language",
        ':createdtime' => "$createdtime",
        ':updatedtime' => "$updatedtime",
      ];
      $sql = <<<HT
      INSERT INTO `transaction`
(
`platform_key`,
`key`,
`string`,
`filepath`,
`filename`,
`language`,
`createdtime`,
`updatedtime`)
VALUES
(
  :platform_key, :key, :string, :filepath, :filename,:language,:createdtime,:updatedtime
);
HT;
      try {
        $this->db->execute($sql, $bindparams); //using bindparams automatically prepares them
      }
      catch (\yii\db\Exception $e) {
        echo "Failed inserting $filepath data into transaction table at time $createdtime due to $e.";
        return "Failed inserting $filepath data into transaction table at time $createdtime due to $e.";
      }
    }
  }

  private function getSubdirectories($dir) {
    $dirs = array_filter(glob("$dir/*"), 'is_dir');
    return $dirs;
  }

  /**
   * Store translations by app
   */
  public function storeTranslationsByApp($appId, $translations) {
    $filename = "Translations_$appId";
    $this->storeTranslationsInFlatFile($filename, $translations);
  }

  /**
   * Store translations by app
   */
  public function getTranslationsByApp($appId) {
    $filename = "Translations_$appId";
    $this->getTranslationsFromFlatFile($filename);
  }

  /**
   * Store json encoded translations in flat file
   * @param type $file
   * @param type $translations
   */
  public function storeTranslationsInFlatFile($filename, $translations) {
    file_put_contents($filename, json_encode($translations));
  }

  /**
   * Get the translations from the flat file
   */
  public function getTranslationsFromFlatFile($filename) {
    return json_decode(file_get_contents($filename), true);
  }

}
