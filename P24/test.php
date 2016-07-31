<?php
$id = 12345;
$pass = "QEA312413121423";
$test = true;

$m = new Merchant($id, $pass, $test);

$acc = $m->account();

$bal = $acc->balance();
$info = $acc->info();
# end test