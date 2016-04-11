<?php

use app\models\Translation;

echo "Parameters you can specify in url are project, host, and resourcePath";

$localhost = "http://localization.dev.itsonsaas.net:8000";
$host = "http://hercules.dev.itsoninc.com:55578";

$project = isset($_GET['project']) ? $_GET['project'] : "myaccount";
$host = isset($_GET['host']) ? $_GET['host'] : $host;

switch ($project) {
  case 'portal' :
    $resourcePath = "/git/saas-portal-adi/src/protected/messages/en"; break;
  case 'myaccount' :
    $resourcePath = "/git/saas-my-adi/yii/messages/myAccount/en"; break;
  default :
    $resourcePath = "/pathtoresources"; break;
}

//specify for uploading the resource path
$resourcePath = isset($_GET['resourcepath']) ? $_GET['resourcepath'] : $resourcePath;

$resourceFilenames = $model->getProjectResourceFileNames($project);

$resourceString = "";
foreach($resourceFilenames as $file) {
  $resourceString .= " -F UploadForm[messageFiles][]=@$resourcePath/$file";
}
  
$calls = [
  
  'upload to saas localization repo' => <<<HT
  curl -X POST -H "Content-type: multipart/form-data" -H "Accept: application/json" -F 'UploadForm[project]=$project'$resourceString $host/translation/upload
HT
  ,
  'upload from localization repo to transifex' => <<<HT
  curl -X GET '$host/translation/uploadtotransifex?project=$project' -v
HT
  ,
  'download from Transifex to localization repo' => <<<HT
  curl -X GET '$host/translation/downloadfromtransifex?project=$project' -v
HT
  ,
  'download zip file from localization repo' => <<<HT
  curl -X POST -d 'DownloadForm[project]=$project' '$host/translation/download' -o $project.zip -v
HT
  
  
];

foreach($calls as $k=>$call) {
  echo "<h3>$k</h3><pre>$call</pre><br /><br />";
}

/**
 * Description of readme
 *
 * @author adityamittal
 */
class readme {
  //put your code here
}
