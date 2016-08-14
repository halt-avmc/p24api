# XML Errors

## Service is not working
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
```xml
<?xml version="1.0" encoding="UTF-8"?>
<response version="1.0">
  <data>
    <error message ="invalid signature" />
  </data>
</response>
```
