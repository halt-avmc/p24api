<?php

namespace P24;

use P24\Balance;

class Merchant {

  private $_id;
  private $_password;
  private $_defaultAcc;
  private $account = [];

  /**
   * @param  array $conf confiuration array for merchant. Keys 'id', 'password'
   * @return void
   */
   public function __construct($conf){
     if(is_array($conf)){
       $this->_id         = array_key_exists("id",       $conf) ? $conf['id']       : false;
       $this->_pass       = array_key_exists("password", $conf) ? $conf['password'] : false;
       $this->_defaultAcc = array_key_exists("account",  $conf) ? $conf['account']  : false;

       $this->account[$this->_defaultAcc] = false;
     }
   }

   /**
    *
    */
   public function setId($id)
   {
     $this->_id=$id;
   }
   public function getId()
   {
     return $this->_id;
   }
   public function setPassword($password)
   {
     $this->_password=$password;
   }
   public function getPassword()
   {
     return $this->_password;
   }

   public function addAccount($acc)
   {
     $this->account[$acc]['card_number']=$acc;
     return $this;
   }

   public function addCard($cardnum)
   {
     $this->addAccount($cardnum);
     return $this;
   }

   public function balance($acc = null)
   {
     $acc = ($acc == null) ?  $this->_defaultAcc : $acc;
     $request = new P24RequestBalance($acc);
     $this->account[$acc] = $response = new P24ResponseBalance($request);
     return $response;
   }
}
