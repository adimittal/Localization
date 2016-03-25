<?php

namespace app\models\baseHttp;

final class HeaderHandler
{
  public static function setHeaderWithDefaultContentType() {
    header('Content-Type: ' . ContentType::TEXT_HTML);
  }

  public static function setHeader($contentType) {
    header('Content-Type: ' . $contentType);
  }

  public static function setContentRangeHeader($data) {
    $length = strlen($data);
    header('Content-Range: bytes 0-' . ($length - 1) . '/' . $length);
    header('Accept-Ranges: bytes');
  }
}
