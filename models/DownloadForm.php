<?php

namespace app\models;

use ZipArchive;
use yii\base\Model;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use yii\base\Exception;

class DownloadForm extends Model {

  public $project;

  public function rules() {
    return [
      ['project', 'required']
    ];
  }

  public function download() {
    $cm = new \app\components\ConfigManager();
    try {
      $projectSlug = $cm->getSlug($this->project);
    }
    catch (Exception $ex) {
      return $ex->getMessage();
    };
    $dirToZip = $cm->getMessageDataPath($projectSlug);
    $zippath = $cm->getDownloadableZipPath();
    $zipFilename = "$zippath/$projectSlug.zip";
    if ($this->validate()) {
      $this->createZipArchive($dirToZip, $zipFilename);
      if (is_file($zipFilename)) {
        $this->downloadZipArchive($zipFilename);
        return true;
      }
      return "Failed creating zip file archive";
    }
    else {
      return false;
    }
  }

  private function downloadZipArchive($archive_file_name) {
    $basename = basename($archive_file_name);
    //then send the headers to force download the zip file
    header("Content-type: application/zip");
    header("Content-Disposition: attachment; filename=$basename");
    header("Pragma: no-cache");
    header("Expires: 0");
    readfile("$archive_file_name");
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
