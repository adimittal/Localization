<?php

namespace app\models;

use ZipArchive;
use yii\base\Model;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Exception;

class DownloadForm extends Model {

  public $project;

  public function rules() {
    return [
      ['project', 'required']
    ];
  }

  public function recurse_copy($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== ( $file = readdir($dir))) {
      if (( $file != '.' ) && ( $file != '..' )) {
        if (is_dir($src . '/' . $file)) {
          recurse_copy($src . '/' . $file, $dst . '/' . $file);
        }
        else {
          copy($src . '/' . $file, $dst . '/' . $file);
        }
      }
    }
    closedir($dir);
    return true;
  }

  public function download() {
    $error = new \app\components\Error();
    $cm = new \app\components\ConfigManager();
    try {
      $projectSlug = $cm->getSlug($this->project);
      $dirToZip = $cm->getMessageDataPath($projectSlug);
      $this->archiveHandler($projectSlug, $dirToZip); //any project specific things before archiving
    }
    catch (Exception $ex) {
      return $error->fail($ex->getMessage());
    };

    $zippath = $cm->getDownloadableZipPath();
    $zipFilename = "$zippath/$projectSlug.zip";
    if ($this->validate()) {
      $this->createZipArchive($dirToZip, $zipFilename);
      if (is_file($zipFilename)) {
        $this->downloadZipArchive($zipFilename);
        return $error;
      }
      return $error->fail("Failed creating zip file archive");
    }
    else {
      return $error->fail("Request could not be validated, maybe missing project name?");
      ;
    }
  }

  private function archiveHandler($projectslug, $dirToZip) {
    if ($projectslug == 'my-account') {
      //copy ar_SA to ar and es_MX to es
      if (!$this->recurse_copy($dirToZip . "/ar_SA", $dirToZip . "/ar") 
        || !$this->recurse_copy($dirToZip . "/es_MX", $dirToZip . "/es")) {
        throw new Exception("Failed to copy to $dirToZip");
      }
    }

    return true;
  }

  private function downloadZipArchive($archive_file_name) {
    $basename = basename($archive_file_name);
    //then send the headers to force download the zip file
    header("Content-type: application/zip");
    header("Content-Disposition: attachment; filename=$basename");
    header("Pragma: no-cache");
    header("Expires: 0");
    readfile("$archive_file_name");
    die;
  }

  private function createZipArchive($dir, $filename) {
    $zip = new ZipArchive();

    if ($zip->open($filename, ZipArchive::CREATE) !== TRUE) {
      exit("cannot open <$filename>\n");
    }

    $fileDir = dirname($filename);
    if (!is_writable($fileDir)) {
      exit("$fileDir is not writable\n");
    }
    // Create recursive directory iterator
    /** @var SplFileInfo[] $files */
    $files = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file) {
      // Skip directories (they would be added automatically)
      if (!$file->isDir()) {
        // Get real and relative path for current file
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($dir) + 1);

        // Add current file to archive
        $zip->addFile($filePath, $relativePath);
      }
    }

    // Zip archive will be created only after closing object
    $zip->close();
  }

}
