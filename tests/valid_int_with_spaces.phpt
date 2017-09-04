--TEST--
valid() and INT validation with spaces
--SKIPIF--
<?php if (!extension_loaded("filter")) die("skip"); ?>
--INI--
precision=14
--FILE--
<?php
$vals = array(
	" 123",
	" 123.01 ",
	"	
   ",
	" ",
	"1234 ",
	1234,
	"       1234           ",
);

$spec = array(
	VALIDATE_INT,
	VALIDATE_FLAG_NONE,
	['min'=>-100, 'max'=>150]
);

foreach ($vals as $var) {
    try {
        var_dump(valid($var, $spec, $status), $status);
    } catch (Exception $e) {
        var_dump($e->getMessage());
    }
}

?>
--EXPECT--
string(85) "Int validation: Invalid int format  (Key: '', Validator: INT, Flags: 0 Value: ' 123')"
string(89) "Int validation: Invalid int format  (Key: '', Validator: INT, Flags: 0 Value: ' 123.01 ')"
string(86) "Int validation: Invalid int format  (Key: '', Validator: INT, Flags: 0 Value: '	
   ')"
string(82) "Int validation: Invalid int format  (Key: '', Validator: INT, Flags: 0 Value: ' ')"
string(86) "Int validation: Invalid int format  (Key: '', Validator: INT, Flags: 0 Value: '1234 ')"
string(82) "Int validation: Too large value  (Key: '', Validator: INT, Flags: 0 Value: '1234')"
string(103) "Int validation: Invalid int format  (Key: '', Validator: INT, Flags: 0 Value: '       1234           ')"
