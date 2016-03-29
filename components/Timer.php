<?php

namespace app\components;

/**
 * @author Aditya Mittal
 * Usage: 
 * $timer = new \app\components\Timer();
 * $timer->log();
 */
class Timer {

  var $start;
  var $pause_time;

  /**
   * Initialize the timer
   */
  public function timer($start = 0) {
    if ($start) {
      $this->start();
    }
  }

  /**
   * start the timer
   */
  public function start() {
    $this->start = $this->get_time();
    $this->pause_time = 0;
  }

  /**
   * pause the timer
   */
  public function pause() {
    $this->pause_time = $this->get_time();
  }

  /**
   * unpause the timer 
   */
  public function unpause() {
    $this->start += ($this->get_time() - $this->pause_time);
    $this->pause_time = 0;
  }

  /**
   * Print the current timer value in webpage
   */
  public function web() {
    echo $this->get();
    echo "<br />";
  }

  /**
   * Log time since timer or start, or start time if given
   * @param type $str - pass in a string to help identify the point at which we're loggint the time
   */
  public function log($str, $start = '') {
    AppLog::log("TIME DURATION FOR $str: " . $this->get($start)) . "SECONDS";
  }

  /**
   * Log time since request started
   * @param type $str - pass in a string to help identify the point at which we're loggint the time
   */
  public function logTimeSinceRequest($str) {
    $timeSinceRequest = $this->get($this->requestTime());
    AppLog::log("TIME SINCE REQUEST AT $str: " . $timeSinceRequest) . "SECONDS";
  }

  /**
   * Get the current timer value, optional second value allows choosing a different start time
   */
  public function get($start = '', $decimals = 8) {
    if (empty($start)) {
      return round(($this->get_time() - $this->start), $decimals);
    }
    return round(($this->get_time() - $start), $decimals);
  }

  /**
   * Get the current time formatted in seconds
   */
  public function get_time() {
    list($usec, $sec) = explode(' ', microtime());
    return ((float) $usec + (float) $sec);
  }

  /**
   * Get the request time
   * @param type $float
   * @return type
   */
  public function requestTime($float = false) {
    return $float ? $_SERVER['REQUEST_TIME_FLOAT'] : $_SERVER['REQUEST_TIME'];
  }

}
