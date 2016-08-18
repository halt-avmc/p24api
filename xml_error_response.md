# XML Errors

## Service is not working
Видимо, иногда сервис мерчантов Приват24 не работает/на профилактике
```xml
<?xml version="1.0" encoding="UTF-8"?>
<response version="1.0">
  <merchant>
    <id>1234</id>
    <signature>2a1f36cadeaf73ba0f95100dcd374e6fcecffaf5</signature>
  </merchant>
  <data>
    <oper>cmt</oper>
    <info>
      <error>Сервис балансов временно недоступен</error>
    </info>
  </data>
</response>
```

## Invalid Signature
Эта ошибка появляется, если передать не правильную сигнатуру запроса
```xml
<?xml version="1.0" encoding="UTF-8"?>
<response version="1.0">
  <data>
    <error message ="invalid signature" />
  </data>
</response>
```

## Unparsable date
Эта ошибка появляется при вызове API `rest_fiz`, если дата в запросе не соответствует шаблону `ДД.ММ.ГГГГ`
```xml
<?xml version="1.0" encoding="UTF-8"?>
<response version="1.0">
  <merchant>
    <id>10343</id>
    <signature>e96665fdd60cbd75d2892a4fcdb8e5460e984fc8</signature>
  </merchant>
  <data>
    <oper>cmt</oper>
    <info>invalid date:Unparseable date: "01-01-2015"</info>
  </data>
</response>
```
