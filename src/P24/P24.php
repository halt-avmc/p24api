<?php

namespace P24;
use \domDocument;

class P24 {
    const VERSION = '0.0.1a';
    const APIURL = "https://api.privatbank.ua/p24api";

    private $_id = '';
    private $_password = '';
    private $_test = 1;

    private $_response = false;

    public function __construct($id='', $pass='')
    {
      $this->_id = $id;
      $this->_password = $pass;
    }

    public function setId($id)
    {
      $this->_id = $id;
    }

    public function setPassword($pass)
    {
      $this->_password = $pass;
    }

    public function balance($card)
    {
      $dataXml = include("p24balance.php");
      $data = preg_replace(
          ["/{test}/"  , "/{card}/"],
          [$this->_test,   $card   ],
          $dataXml);

    	$sign = $this->calcSignature($data);

      $reqXml = include("p24request.php");
      $request = preg_replace(
          ["/{id}/"  , "/{signature}/", "/{data}/"],
          [$this->_id,   $sign        ,   $data   ],
          $reqXml);

      $this->_response = \Httpful\Request::post(self::APIURL."/balance")->body($request)->sendsXml()->send();

      return $this->_response;
    }

    public function checkResponse()
    {
      if ($this->_response != false)
      {
        $data = $this->getTextBetweenTags("data", $this->_response, 1);
        $data = preg_replace(["/<data>/","/<\/data>/"], ["",""], $data);
        $hash = $this->calcSignature($data[0]);
        $xml=simplexml_load_string($this->_response);
        if ($hash === $xml->merchant->signature.'')
          return true;
      }
      return false;
    }


    private function getTextBetweenTags($tag, $html, $strict=0)
    {
        /*** a new dom object ***/
        $dom = new domDocument;

        /*** load the html into the object ***/
        if($strict==1)
        {
            $dom->loadXML($html);
        }
        else
        {
            $dom->loadHTML($html);
        }

        /*** discard white space ***/
        $dom->preserveWhiteSpace = false;

        /*** the tag by its tag name ***/
        $content = $dom->getElementsByTagname($tag);

        /*** the array to return ***/
        $out = array();
        foreach ($content as $item)
        {
            /*** add node value to the out array ***/
            $out[] = $item->C14N();
        }
        /*** return the results ***/
        return $out;
    }

    private function calcSignature($data)
    {
      return sha1(md5($data.$this->_password));
    }
}
