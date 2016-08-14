<?php

namespace halt\P24;

use Httpful\Request;
use \SimpleXMLElement;
use \DOMDocument;

class Account
{
  protected $rawXml;

  const STATUS_NEW="NEW";
  const STATUS_ERR="ERR";
  const STATUS_OK ="OK";

  protected $status;
  protected $merchant;

  protected $balance_uri = "https://api.privatbank.ua/p24api/balance";

  protected $balance = [
    'av_balance'=>null,   // Доступные средства. Это средства, которыми можно оперировать
    'bal_date'=>null,     // Дата баланса
    'bal_dyn'=>null,      // ??
    'balance'=>null,      // Полный баланс. Сюда входят, в т.ч. средства, заблокированные на карте (HOLD ?)
    'fin_limit'=>null,    // Кредитный лимит. Например на кредитной карте
    'trade_limit'=>null,  // ??
  ];

  protected $info = [
    'account'=>null,          // Счет карты
    'acc_name'=>null,         // Название счёта ("Виртуальный счет Приват24")
    'acc_type'=>null,         // Тип счёта  (CM, CC, ??)
    'card_number'=>null,      // Номер карты
    'main_card_number'=>null, // Номер основной карты
    'card_type'=>null,        // Тип карты ("Карта для выплат")
    'currency'=>null,         // Валюта
    'card_stat'=>null,        // Статус карты (NORM - всё ОК, RSTR - заблокирована, ??)
    'src'=>null,              // ?? (M)
  ];

  private function balanceXml()
  {
    $id   = $this->merchant->id();
    $wait = $this->merchant->wait();
    $test = $this->merchant->test();
    $oper = "cmt";

    $request = new SimpleXMLElement("<request />");
	     $request->addAttribute("version", "1.0");

    $merchant = $request->addChild("merchant");
	     $merchant->addChild("id", $id);

    $data = $request->addChild("data");
    	$data->addChild("oper", $oper);
    	$data->addChild("wait", $wait);
    	$data->addChild("test", $test);

    $payment = $data->addChild("payment");

    if (isset($this->info['account']))
    {
      $acc = $this->info['account'];
      $prop = $payment->addChild("prop");
        $prop->addAttribute("name", "cardnum");
        $prop->addAttribute("value", $acc);

      $prop = $payment->addChild("prop");
        $prop->addAttribute("name", "country");
        $prop->addAttribute("value", "UA");
    }

    $signature = $this->calcDataSignature($data);
    $merchant->addChild("signature", $signature);

    // $xml essentially is the same as $request
    // just is DOMDocument instead of SimpleXMLElement
    // This is because saveXML() function with LIBXML_NOEMPTYTAG
    // exists only in DOM and not in SimpleXML
    $xml = dom_import_simplexml($request);
    return $xml->ownerDocument->saveXML($xml, LIBXML_NOEMPTYTAG);
  }

  private function calcDataSignature($data)
  {
    $xml = dom_import_simplexml($data);
    $inner_xml='';
    $children=$xml->childNodes;
    foreach($children as $node)
        $inner_xml .= $node->ownerDocument->saveXML($node, LIBXML_NOEMPTYTAG);

    return $this->merchant->calcSignature($inner_xml);
  }

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

    $xml_request = $this->balanceXml();
    $response = \Httpful\Request::post($this->balance_uri)->body($xml_request)->sendsXml()->expectsXml()->send();
    $this->rawXml = $response->raw_body;

    list( ,$d) = each($response->body->xpath('data'));
    list( ,$s) = each($response->body->xpath('merchant/signature'));

    //if ($this->calcDataSignature($d) == $s);

    list( ,$info) = each($response->body->xpath('data/info'));

    foreach($info->children() as $k=>$child)
    {
    	$name = $child->getName();
    	if ($name=="error")
    	{
    	    $this->status = self::STATUS_ERR;
    	    return false;
    	}
    }

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

  public function xml()
  {
    if ($this->status==self::STATUS_NEW)
        $this->balance();

    return $this->rawXml;
  }
}
