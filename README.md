# Privat24

Это PHP API для мерчанта [Privat24](https://www.privat24.ua/).

Инструкция по установке находится [здесь](https://github.com/halt-avmc/p24api/releases/latest).

# Использование
Данная библиотека работает **_только_** совместно с [Composer](https://getcomposer.org/download/)'ом
```PHP
<?php
  require (vendor/autoload.php);

  use halt\Merchant

  $id = <Merchant Id>;
  $password = "<Merchant Password>";

  $merchant = new Merchant(['id'=>$id, 'password'=>$password]);

  // Баланс и информация о карте мерчанта по-умолчанию
  // которая привязана к мерчанту
  $balance = $merchant->balance();
  $info    = $merchant->info();

  // Кроме карты по-умолчанию можно получить информацию и баланс
  // о других картах мерчанта
  $card = "<Any merchant card or account>";
  $balance = $merchant->account($card)->balance();
  $info    = $merchant->account($card)->info();
?>
```
Возвращаемые значения - это массив значений, которые описаны на сайте [API Приват24](https://api.privatbank.ua/balance.html)
```PHP
$balance = [
  'av_balance'   // Доступные средства. Это средства, которыми можно оперировать
  'bal_date'     // Дата баланса
  'bal_dyn'      // ?? - описание на сайте отсутсвует
  'balance'      // Полный баланс. Сюда входят, в т.ч. средства, заблокированные на карте (HOLD)
  'fin_limit'    // Кредитный лимит. Например на кредитной карте
  'trade_limit'  // ?? - описание на сайте отсутсвует
];

$info = [
  'account'          // Счет карты
  'acc_name'         // Название счёта ("Виртуальный счет Приват24")
  'acc_type'         // Тип счёта  (CM, CC, ??)
  'card_number'      // Номер карты
  'main_card_number' // Номер основной карты
  'card_type'        // Тип карты ("Карта для выплат")
  'currency'         // Валюта (UAH, USD, EUR, ...)
  'card_stat'        // Статус карты (NORM - всё ОК, RSTR - заблокирована, ??)
  'src'              // ?? - описание на сайте отсутсвует (M)
];
```

Если во время запроса произошла ошибка, то в ответе вернётся не массив значений, а сообщение от банка с описанием ошибки:
```
invalid signature
```
или такое
```
Сервис балансов временно недоступен
```
Если в ответ сервера будет подписан не верной сигнатурой (проверяется библиотекой), то в ответе будет содержаться
```
Wrong response signature!
```
