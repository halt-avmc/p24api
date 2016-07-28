<?php

namespace halt\P24;

use \Httpful\Request;

class P24Request extends Request
{
  const APIURL = "https://api.privatbank.ua/p24api/";
  protected $version = "1.0";

  public function __construct()
  {
    $template = self::init()
      ->uri(self::APIURL)         // Set request uri
      ->method(Http::POST)        // Alternative to Request::post
      ->expectsXml();             // Expect HTML responses

      // Set it as a template
      self::ini($template);
  }
}
