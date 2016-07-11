<?php

namespace P24;
use P24Data;

class Balance extends P24Data
{
  protected $wait;
  protected $test;
  protected $payments = [];

  protected static $xmlRequest = '<?xml version="1.0" encoding="UTF-8"?>
  <request version="1.0">
    <merchant>
      <id></id>
      <signature></signature>
    </merchant>
    <data>
      <oper>cmt</oper>
      <wait></wait>
      <test></test>
      <payment id="">
        <prop name="cardnum" value="" />
        <prop name="country" value="UA" />
      </payment>
    </data>
  </request>';

  public function __construct($wait=0, $test = false){
    $this->wait = $wait;
    $this->test = $test;
    parent::__construct("cmt");

    $this->data->addChild("wait", $wait);
    $this->data->addChild("test", $test?1:0);

    $dataFields = array_merge($this->dataFields,
    [
      'wait',
      'test',
      'payment'=>[
        'prop'=>'@cardnum',
        'prop'=>'@country',
      ],
    ]);
  }

  public static function getXML(){
    return self::$xmlRequest;
  }

  /**
   * @param  string $card
   * @return void
   */
  public function addCard($card, $payId=NULL){
    if ($payId === NULL)
      $this->payments[] = $card;
    else
      $this->payments[$payId] = $card;
  }

  /**
   * @param  string $acc
   * @return void
   */
  public function addAccount($acc, $payId=NULL){
    $this->addCard($acc, $payId);
  }

  public function serialize()
  {
    $wait = "<wait>$this->wait</wait>\n";
    $test = "<test>$this->test</test>\n";
    $payments = "";
    foreach ($this->payments as $id => $value) {
      $payment = "<payment id=\"$id\">\n";
      $payment.= "<prop name=\"cardnum\" value=\"$value\" />\n";
      $payment.= "<prop name=\"country\" value=\"UA\" />\n";
      $payment.= "</payment>\n";

      $payments.= $payment;
    }

    return parent::serialize().$wait.$test.$payments;
  }
}
