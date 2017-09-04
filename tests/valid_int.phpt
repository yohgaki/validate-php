--TEST--
valid() and INT validation
--SKIPIF--
<?php if (!extension_loaded("filter")) die("skip"); ?>
--INI--
precision=14
--FILE--
<?php
$vals = array(
	"123",
	123,
	123.0, // treats integer float as int
	"+123",
	+123,
	+123.0,
	"-123",
	-123,
	-123.0,
	"1234",
	1234,
	1234.0,
	"123.4",
	123.4,
	123.40,
	"123.4.5",
);

$spec = array(
	VALIDATE_INT,
	VALIDATE_FLAG_NONE,
	['min'=>-150, 'max'=>150]
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
int(123)
bool(true)
int(123)
bool(true)
int(123)
bool(true)
int(123)
bool(true)
int(123)
bool(true)
int(123)
bool(true)
int(-123)
bool(true)
int(-123)
bool(true)
int(-123)
bool(true)
string(82) "Int validation: Too large value  (Key: '', Validator: INT, Flags: 0 Value: '1234')"
string(82) "Int validation: Too large value  (Key: '', Validator: INT, Flags: 0 Value: '1234')"
string(82) "Int validation: Too large value  (Key: '', Validator: INT, Flags: 0 Value: '1234')"
string(86) "Int validation: Invalid int format  (Key: '', Validator: INT, Flags: 0 Value: '123.4')"
string(86) "Int validation: Invalid int format  (Key: '', Validator: INT, Flags: 0 Value: '123.4')"
string(86) "Int validation: Invalid int format  (Key: '', Validator: INT, Flags: 0 Value: '123.4')"
string(88) "Int validation: Invalid int format  (Key: '', Validator: INT, Flags: 0 Value: '123.4.5')"
