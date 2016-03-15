<?php

namespace app\commands;

use yii\console\Controller;

class TransifexController extends Controller {

  /**
   * This command echoes what you have entered as the message.
   * @param string $message the message to be echoed.
   */
  public function actionIndex($message = 'hello world') {
    echo $message . "\n";
  }

  /**
   * When we download translations from transifex, the filenames are awkward, this command renames them
   * @param type $myAccountDir - the path to saas-my-{$user} example:  "/git/saas-my-adi"
   * @param string $lang - example 'ar' to update the arabic filenames
   */
  public function actionMyAccountDownloadsRename($myAccountDir, $lang) {
    $lang_dir = "$myAccountDir/yii/messages/myAccount/$lang";
    foreach (glob($lang_dir . "/*.php") as $file) {
      preg_match_all('/_(my-account_(.*php))_/iU', $file, $result);
      if (isset($result[2][0])) {
        $filephp = $result[2][0]; //forms_localephp, content_localephp ...
        $filename = implode(".php", explode("php", $filephp));
        `cd $lang_dir`;
        `mv "$file" "$lang_dir/$filename"`;
      }
    }
  }

}
