<?php

namespace halt\P24;

use halt\P24\P24Request;

class Balance extends P24Request
{
  const BALANCE = 'balance';
  protected $acc;
  protected $wait;
  protected $test;

  protected static $xmlRequest = '<?xml version="1.0" encoding="UTF-8"?>
  <request version="{{version}}">
    <merchant>
      <id>{{merchant_id}}</id>
      <signature>{{calculated_signature}}</signature>
    </merchant>
    <data>
      <oper>cmt</oper>
      <wait>{{wait}}</wait>
      <test>{{merchant_test}}</test>
      <payment id="{{payment_id}}">
        <prop name="cardnum" value="{{merchant_account}}" />
        <prop name="country" value="UA" />
      </payment>
    </data>
  </request>';

  public function __construct($acc, $wait, $test){
    $this->acc  = $acc;
    $this->wait = $wait;
    $this->test = $test;
  }

  public static function getXML(){
    return self::$xmlRequest;
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
