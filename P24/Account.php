<?php

namespace halt\P24;

class Account
{
  /*
  <account>5168742060221193</account>
  <card_number>5168742060221193</card_number>
  <acc_name>Карта для Выплат Gold</acc_name>
  <acc_type>CC</acc_type>
  <currency>UAH</currency>
  <card_type>Карта для Выплат Gold</card_type>
  <main_card_number>5168742060221193</main_card_number>
  <card_stat>NORM</card_stat>
  <src>M</src>
  */
  protected $rawXml;
  protected $status;

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

  }
}
