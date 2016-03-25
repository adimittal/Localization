<?php

namespace app\components;

/**
 * AppLog class
 * The following class abstracts data stored in Yii::log() and Yii::trace()
 * @author Eugene Voznesensky
 */
class AppLog {

  private static $dev = false;
  private static $hostname;
  private static $servername;
  private static $requestURI;
  private static $fullURI;
  private static $frameNo = 1;
  
  public static function log($msg = '', $level = CLogger::LEVEL_INFO, $category = 'application', $debug = false, $origin = null) {
    if (!is_string($msg)) {
      $msg = print_r($msg, true);
    }
    self::$dev = $debug;
    self::$hostname = gethostname();
    self::$servername = $_SERVER['SERVER_NAME'];
    self::$requestURI = $_SERVER['REQUEST_URI'];
    self::$fullURI = self::full_url(false);
    self::clean($msg);

    if ($debug) {
      Yii::log(print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true));
    }
    if (!$origin) {
      $callers = debug_backtrace();
      $origin = self::calc_origin($callers);
    }
    $msg = self::$hostname . " : " . self::$fullURI . " : $origin : " . $msg;
    Yii::log(print_r($msg, true), $level, $category);
  }

  private static function calc_origin(&$callers) {
    $frameNo = self::$frameNo;
    $method = $callers[$frameNo]['function'];
    $class = 'ClassNotSet';
    if (array_key_exists('class', $callers[$frameNo])) {
      $class = $callers[$frameNo]['class'];
    }
    else {
      if (array_key_exists('file', $callers[$frameNo])) {
        $class = $callers[$frameNo]['file'];
      }
    }
    $file = 'FileNameNotSet';
    if (array_key_exists('file', $callers[$frameNo])) {
      $file = $callers[$frameNo]['file'];
    }
    $line = "LineNumberNotSet";
    if (array_key_exists('line', $callers[$frameNo])) {
      $line = $callers[$frameNo]['line'];
    }
    return "$class->$method File: $file Line: $line:";
  }

  public static function trace($msg, $category = 'application') {
    self::clean($msg);
    Yii::trace(print_r($msg, true), $category);
  }

  /**
   * The log is expanding a lot because the saas requests have these action url pairs - we can easily see them in main.config, no need to log repeatedly
   * @param type $msg
   * @return type
   */
  public static function removeActionURLPairs($msg) {
    if (self::$dev) {
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
    if (self::$dev) {
      return $msg;
    }
    return preg_replace('/authorization:Bearer .*/i', 'authorization:Bearer xxx', $msg);
  }

  private static function clean(&$msg) {
    if (self::$dev) {
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
      if (preg_match('/\[?(password|access_token|passwordhash|client_secret|client_id|clientId|clientSecret|newpassword)\].*?=>(.*)/i', $line, $patternMatches)) {
        // 2. Replace
        $line = '[' . $patternMatches[1] . ']' . ' => xxx';
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
      Yii::log($msg, $level, $category);
    }
  }

  private static function url_origin($use_forwarded_host = false) {
    $s = $_SERVER;
    $ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true : false;
    $sp = strtolower($s['SERVER_PROTOCOL']);
    $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
    $port = $s['SERVER_PORT'];
    $port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
    $host = ($use_forwarded_host && isset($s['HTTP_X_FORWARDED_HOST'])) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
    $host = isset($host) ? $host : $s['SERVER_NAME'] . $port;
    return $protocol . '://' . $host;
  }

  private static function full_url($use_forwarded_host = false) {
    return self::url_origin($use_forwarded_host) . $_SERVER['REQUEST_URI'];
  }

}
