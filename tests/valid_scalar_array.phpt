--TEST--
valid() and scalar array
--SKIPIF--
<?php if (!extension_loaded("validate")) die("skip"); ?>
--INI--
error_reporting = -1
--FILE--
<?php

$var = 12;
$res = valid($var, [VALIDATE_INT, VALIDATE_INT_ALLOW_OCTAL,
					['min'=>0, 'max'=>20]], $status);
var_dump($res);

try {
	$var = array(12);
	$res = valid($var, [VALIDATE_INT, VALIDATE_INT_ALLOW_OCTAL,
						['min'=>0, 'max'=>20]], $status);
	var_dump($res);
} catch (Exception $e) {
	var_dump($e->getMessage());
}

try {
	$var = 12;
	$res = valid($var, [VALIDATE_INT, VALIDATE_INT_ALLOW_OCTAL|VALIDATE_FLAG_REQUIRE_ARRAY,
						['min'=>0, 'max'=>20, 'amin'=>0, 'amax'=>20]], $status);
	var_dump($res);
} catch (Exception $e) {
	var_dump($e->getMessage());
}


try {
	$var = array(12);
	$res = valid($var, [VALIDATE_INT, VALIDATE_FLAG_NONE|VALIDATE_FLAG_REQUIRE_ARRAY,
						['min'=>0, 'max'=>20, 'amin'=>0, 'amax'=>20]], $status);
	var_dump($status, $res);
} catch (Exception $e) {
	var_dump($e->getMessage());
}



try {
	$var = array(12,13,20);
	$res = valid($var, [VALIDATE_INT, VALIDATE_INT_ALLOW_OCTAL|VALIDATE_FLAG_REQUIRE_ARRAY,
						['min'=>0, 'max'=>20, 'amin'=>0, 'amax'=>20]], $status);
	var_dump($status, $res);
} catch (Exception $e) {
	var_dump($e->getMessage());
}



?>
--EXPECT--
int(12)
string(124) "Scalar value expected. Array is given. Need VALIDATE_ARRAY as parent spec?  (Key: '', Validator: INT, Flags: 2 Value: 'N/A')"
int(12)
bool(true)
array(1) {
  [0]=>
  int(12)
}
bool(true)
array(3) {
  [0]=>
  int(12)
  [1]=>
  int(13)
  [2]=>
  int(20)
}
