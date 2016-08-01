<?php

namespace halt\P24;

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
    'date'=>null,
    'dyn'=>null,
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
    if (is_set($acc))
    {
      $xml_card = "<prop name="cardnum" value=\"$acc\" />";
      $xml_country = "<prop name=\"country\" value=\"UA\" />";

      $xml_payment = "<payment>$xml_card $xml_country</payment>";
    }

    $xml_data = $xml_oper . $xml_wait . $xml_test . $xml_payment;
    $signature = $this->merchant->calcSignature($xml_data);

    $id = $this->merchant->id();
    $xml_merchant = "<merchant><id>$id</id><signature>$signature</signature></merchant>";

    $xml_request = "<request version=\"1.0\">$xml_merchant $xml_data</request>";

    return $xml_request;
    return $this->balance; // <== This is what is actual should be returned after testing;
  }

  public function info()
  {
    if ($this->status!=self::STATUS_OK)
        $this->balance();

    return $this->info;
  }
}
