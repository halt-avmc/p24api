<?php

namespace P24;

class P24Data
{
  protected $oper;
  protected $data;
  protected $dataFields = [
    'oper',
  ];

  protected $requestFields = [
    'oper',
  ];


  public function __construct($data)
  {
    if(is_array($data))
    {
      foreach($data as $key=>$value)
        if(in_array($key, $this->requestFields))
          $this->$key = $value;
    }
    //$this->oper = $oper;
  }

  public function serialize()
  {
    return "<oper>$this->oper</oper>\n";
  }

  public function signature($password)
  {
    return sha1(md5($this->serialize().$password));
  }
}
