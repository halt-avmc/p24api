<?php

namespace halt\P24;

use Httpful\Request;

class Account
{
  protected $rawXml;

  const STATUS_NEW="NEW";
  const STATUS_ERR="ERR";
  const STATUS_OK ="OK";

  protected $status;
  protected $merchant;

  protected $balance = [
    'av_balance'=>null,
    'bal_date'=>null,
    'bal_dyn'=>null,
    'balance'=>null,
    'fin_limit'=>null,
    'trade_limit'=>null,
  ];
  
  protected $info = [
    'account'=>null,
    'card_number'=>null,
    'acc_name'=>null,
    'acc_type'=>null,
    'currency'=>null,
    'card_type'=>null,
    'main_card_number'=>null,
    'card_stat'=>null,
    'src'=>null,
  ];

  public function __construct($merchant, $account = null)
  {
    $this->merchant = $merchant;
    $this->info['account'] = $account;
    $this->status = self::STATUS_NEW;
  }

  public function balance()
  {
    if ($this->status==self::STATUS_OK)
      return $this->balance;

    $wait = $this->merchant->wait();
    $test = $this->merchant->test();
    $oper = "cmt";

    $xml_oper = "<oper>$oper</oper>";
    $xml_wait = "<wait>$wait</wait>";
    $xml_test = "<test>$test</test>";

    $xml_payment = "<payment />";
    if (isset($acc))
    {
      $xml_card = "<prop name=\"cardnum\" value=\"$acc\" />";
      $xml_country = "<prop name=\"country\" value=\"UA\" />";

      $xml_payment = "<payment>$xml_card $xml_country</payment>";
    }

    $xml_inner_data = $xml_oper . $xml_wait . $xml_test . $xml_payment;
    $xml_data = "<data>$xml_inner_data</data>";

    $id = $this->merchant->id();
    $signature = $this->merchant->calcSignature($xml_inner_data);
    $xml_merchant = "<merchant><id>$id</id><signature>$signature</signature></merchant>";

    $xml_request = "<request version=\"1.0\">$xml_merchant $xml_data</request>";

    $uri = "https://api.privatbank.ua/p24api/balance";
    $response = \Httpful\Request::post($uri)->body($xml_request)->sendsXml()->expectsXml()->send();

    $this->rawXml = $response->raw_body;

    foreach ($response->body->data->info->cardbalance->children() as $key=>$value)
    {
      if ($key=="card")
      {
        foreach ($value->children() as $k=>$v)
          $this->info[$k]=(string)$v;
        continue;
      }
      $this->balance[$key]=(string)$value;
    }
    
    $this->status=self::STATUS_OK;
    return $this->balance;
  }

  public function info()
  {
    if ($this->status!=self::STATUS_OK)
        $this->balance();

    return $this->info;
  }
}
