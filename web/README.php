<?php

$localhost = "http://localization.dev.itsonsaas.net:8000";
$host = "http://hercules.dev.itsoninc.com:55578";


$project = isset($_GET['project']) ? $_GET['project'] : "myaccount";
$host = isset($_GET['host']) ? $_GET['host'] : $host;


$calls = [
  
  'upload to saas localization repo' => <<<HT
  curl -X POST -H "Content-type: multipart/form-data" -H "Accept: application/json" -F 'UploadForm[project]=$project' -F UploadForm[messageFiles][]=@/git/saas-my-adi/yii/messages/myAccount/en/content_locale.php -F UploadForm[messageFiles][]=@/git/saas-my-adi/yii/messages/myAccount/en/forms_locale.php $host/translation/upload
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