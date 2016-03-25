<?php

namespace app\models\baseHttp;

/**
 * Final class to use HTTP ContentType's as constants throughout the application
 * @Author: Gaurav Kumkar <gaurav.kumkar@itsoninc.com>
 */
final class ContentType {

  const TEXT_HTML = 'text/html';
  const TEXT_JAVASCRIPT = 'text/javascript';
  const JSON = 'application/json';
  const X_WWW_FORM_URL_ENCODED = 'application/x-www-form-urlencoded';
  const OCTET_STREAM = 'application/octet-stream';
  const MULTIPART_FORM_DATA = 'multipart/form-data';

}