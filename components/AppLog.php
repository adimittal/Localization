<?php

namespace app\components;

use Yii;
use yii\base\Component;

/**
 * AppLog class
 * The following class abstracts data stored in Yii::log() and Yii::trace()
 * @author Eugene Voznesensky
 */
class AppLog extends Component {
  private static $dev = 0;

  public static function log($msg = '', $level = '', $category = 'application', $debug = false, $origin = null) {
    self::$dev = $debug;
    $msg = print_r($msg, true);
    //self::clean($msg);

    if($debug) {
      Yii::log(print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true));
    }
    $frameNo = 1;
    if(!$origin) {
      $callers = debug_backtrace();
      $method  = $callers[$frameNo]['function'];
      $class = 'ClassNotSet';
      if(array_key_exists('class', $callers[$frameNo])) {
        $class = $callers[$frameNo]['class'];
      }
      else {
        if(array_key_exists('file', $callers[$frameNo])) {
          $class = $callers[$frameNo]['file'];
        }
      }
      $line    = "LineNumberNotSet";
      if(array_key_exists('line', $callers[$frameNo])) {
        $line = $callers[$frameNo]['line'];
      }
      $origin = "Line: $line: $class->$method";
    }
    Yii::error(print_r($msg, true), $category);
  }

  public static function trace($msg, $category = 'application') {
   // self::clean($msg);
    Yii::trace(print_r($msg, true), $category);
  }

  /**
   * The log is expanding a lot because the saas requests have these action url pairs - we can easily see them in main.config, no need to log repeatedly
   * @param type $msg
   * @return type
   */
  public static function removeActionURLPairs($msg) {
    if(self::$dev) {
      return $msg;
    }
    return preg_replace('/\[actionUrlPair\:protected\] =\> Array.*\)/isU', '', $msg);
  }

  /**
   * Removing Authorization Bearer token for security purpose
   * @param type $msg
   * @return type
   */
  public static function removeAuthorizationBearer($msg) {
    if(self::$dev) {
      return $msg;
    }
    return preg_replace('/authorization:Bearer .*/i', 'authorization:Bearer xxx', $msg);
  }

  public static function clean(&$msg) {
    if(self::$dev) {
      return $msg;
    }
    $cleanMsg = '';

    // 1. Split message into lines
    $msg = str_replace(',', "," . PHP_EOL, $msg);
    $msg = str_replace('&', PHP_EOL . "&", $msg);
    foreach (preg_split("/((\r?\n)|(\r\n?))/", $msg) as $line) {
      if (preg_match('/( +)\[?(password|access_token|passwordhash|client_secret|client_id|clientId|clientSecret|newpassword)/', $line, $patternMatches)) {
        // 2. Replace
        $line = $patternMatches[1] . '[' . $patternMatches[2] . ']' . ' => xxx';
      }

      if (preg_match('/&(password|access_token|passwordhash|client_secret|client_id|clientId|clientSecret|newpassword)=/', $line, $patternMatches)) {
        // 2. Replace
        $line = $patternMatches[0] . 'xxx';
      }

      if (preg_match('/"(password|access_token|passwordhash|client_secret|client_id|clientId|clientSecret|newpassword)/', $line, $patternMatches)) {
        // 2. Replace
        $line = $patternMatches[0] . '":xxx,';
      }

      $cleanMsg .= $line . PHP_EOL;
    }
    if (strstr($cleanMsg, PHP_EOL . '&')) {
      $cleanMsg = str_replace(PHP_EOL . '&', '&', $cleanMsg);
    }

    $msg = $cleanMsg;
  }

  public static function fpLog($msg = '', $method = '', $file = '', $line = '') {
    if (YII_DEBUG) {
      $level = 'info';
      $category = 'forgotPassword';
      $msg .= ', ' . $method . ' in ' . $file . ' (' . $line . ')';
      Yii::error($msg, $level, $category);
    }
  }

}
