--TEST--
valid_id()
--SKIPIF--
<?php if (!extension_loaded("validate")) die("skip"); ?>
--FILE--
<?php

var_dump(valid_id('UNDEFINED'));
var_dump(valid_id('NULL'));
var_dump(valid_id('BOOL'));
var_dump(valid_id('INT'));
var_dump(valid_id('FLOAT'));
var_dump(valid_id('STRING'));
var_dump(valid_id('REGEXP'));
var_dump(valid_id('URL'));
var_dump(valid_id('EMAIL'));
echo "********* error ***********\n";
var_dump(valid_id('ARRAY'));
var_dump(valid_id(NULL));
var_dump(valid_id(array()));
var_dump(valid_id(123));
var_dump(valid_id(new StdClass));

echo "Done\n";
?>
--EXPECTF--
int(1)
int(2)
int(3)
int(4)
int(5)
int(6)
int(8)
bool(false)
bool(false)
********* error ***********
bool(false)
bool(false)

Warning: valid_id() expects parameter 1 to be string, array given in %s on line 15
NULL
bool(false)

Warning: valid_id() expects parameter 1 to be string, object given in %s on line 17
NULL
Done
