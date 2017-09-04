--TEST--
valid() and array with reference
--SKIPIF--
<?php if (!extension_loaded("filter")) die("skip"); ?>
--FILE--
<?php

$array = ["123"];
$array2 = [&$array];
var_dump(valid($array2, [VALIDATE_INT, VALIDATE_FLAG_REQUIRE_ARRAY,
						 ['min'=>-100,'max'=>100, 'amin'=>0, 'amax'=>10]], $status,
			   VALIDATE_OPT_DISABLE_EXCEPTION));
var_dump($array, $array2);

// Reference is not fully supported. So array to string conversion raised

?>
--EXPECTF--
Notice: Array to string conversion in %s on line 7
NULL
array(1) {
  [0]=>
  string(3) "123"
}
array(1) {
  [0]=>
  &array(1) {
    [0]=>
    string(3) "123"
  }
}
