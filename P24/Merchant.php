<?php

namespace halt\P24;

use halt\P24\Account;

class Merchant {

  private $_id;
  private $_password;
  private $_test;
  private $_wait;

  private $_account = ['default'=>null];

  /**
   * @param  array $conf confiuration array for merchant. Keys 'id', 'password', 'account', 'test'
   * @return void
   */
   public function __construct($conf){
     if(is_array($conf)){
       $this->_id       = array_key_exists("id",       $conf) ? $conf['id']       : false;
       $this->_password = array_key_exists("password", $conf) ? $conf['password'] : false;
       $this->_test     = array_key_exists("test",     $conf) ? $conf['test']     : false;
       $this->_wait     = array_key_exists("wait",     $conf) ? $conf['wait']     : 0;
     }
     $this->_account['default'] = new Account($this);
   }

   public function id()
   {
     return $this->_id;
   }

   public function test()
   {
     return $this->_test;
   }

   public function wait()
   {
     return $this->_wait;
   }

   public function account($acc = null)
   {
     if (is_set($acc))
       return array_key_exists($acc, $this->_account) ? $this->_account[$acc] : $this->_account[$acc] = new Account($this, $acc);
     else
       return $this->_account['default'] = new Account($this);
   }

   public function balance()
   {
     return $this->_account['default']->balance();
   }

   public function calcSignature($data)
   {
     return sha1(md5($data.$this->_password));
   }
}
